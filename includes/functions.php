<?php
// includes/functions.php

// ---------------------------------------------------------
// Coin validation
// ---------------------------------------------------------
function isValidCoin(int $coin): bool {
    $allowedCoins = [1, 5, 10, 20];
    return in_array($coin, $allowedCoins, true);
}

// ---------------------------------------------------------
// Device status
// ---------------------------------------------------------
function markDeviceSeen(mysqli $conn): void {
    $result = $conn->query(
        "INSERT INTO device_status (id, last_seen) VALUES (1, NOW())
         ON DUPLICATE KEY UPDATE last_seen = NOW()"
    );

    if (!$result) {
        error_log("markDeviceSeen failed: " . $conn->error);
    }
}

function getDeviceStatus(mysqli $conn, int $thresholdSeconds = 30): array {
    $result = $conn->query("SELECT last_seen FROM device_status WHERE id = 1");

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    if ($result->num_rows === 0) {
        return ["online" => false, "last_seen" => "Never"];
    }

    $lastSeen = $result->fetch_assoc()['last_seen'];
    $secondsAgo = time() - strtotime($lastSeen);

    return [
        "online" => $secondsAgo <= $thresholdSeconds,
        "last_seen" => date("g:i:s A", strtotime($lastSeen)),
    ];
}

// ---------------------------------------------------------
// Coin totals
// ---------------------------------------------------------
function getTotalSavings(mysqli $conn): float {
    // Net total (inserted coins minus withdrawn coins)
    $result = $conn->query(
        "SELECT
            COALESCE((SELECT SUM(coin_value) FROM transactions), 0)
          - COALESCE((SELECT SUM(coin_value * coin_count) FROM coin_withdrawal_items), 0)
          AS total"
    );

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $total = (float) $result->fetch_assoc()['total'];
    return max(0.0, $total);
}

function getTotalCoinCount(mysqli $conn): int {
    // Net total coin pieces (inserted pieces minus withdrawn pieces)
    $result = $conn->query(
        "SELECT
            COALESCE((SELECT COUNT(*) FROM transactions), 0)
          - COALESCE((SELECT SUM(coin_count) FROM coin_withdrawal_items), 0)
          AS total_coins"
    );

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $totalCoins = (int) $result->fetch_assoc()['total_coins'];
    return max(0, $totalCoins);
}

function getCoinCounts(mysqli $conn): array {
    // Net remaining coin pieces per denomination
    $counts = [];

    foreach ([1, 5, 10, 20] as $value) {
        $stmt = $conn->prepare(
            "SELECT
                COALESCE((SELECT COUNT(*) FROM transactions WHERE coin_value = ?), 0)
              - COALESCE((SELECT SUM(coin_count) FROM coin_withdrawal_items WHERE coin_value = ?), 0)
              AS cnt"
        );

        $stmt->bind_param("ii", $value, $value);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $counts[$value] = max(0, (int) $row['cnt']);

        $stmt->close();
    }

    return $counts;
}

// ---------------------------------------------------------
// Recent transactions
// ---------------------------------------------------------
function getRecentTransactions(mysqli $conn, int $limit = 10): array {
    $stmt = $conn->prepare(
        "SELECT coin_value, created_at FROM transactions
         ORDER BY created_at DESC, id DESC LIMIT ?"
    );
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = [
            "coin_value" => (int) $row['coin_value'],
            "created_at" => date("M d, Y g:i A", strtotime($row['created_at'])),
        ];
    }

    $stmt->close();
    return $transactions;
}

// ---------------------------------------------------------
// Insert a coin
// ---------------------------------------------------------
function insertTransaction(mysqli $conn, int $coin): bool {
    $stmt = $conn->prepare("INSERT INTO transactions (coin_value) VALUES (?)");
    $stmt->bind_param("i", $coin);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

// ---------------------------------------------------------
// Pagination + filtering (used by history.php)
// ---------------------------------------------------------
function getTransactionCount(mysqli $conn, int $coinFilter = 0): int {
    if ($coinFilter > 0) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM transactions WHERE coin_value = ?");
        $stmt->bind_param("i", $coinFilter);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM transactions");
    }

    $stmt->execute();
    $count = (int) $stmt->get_result()->fetch_assoc()['cnt'];
    $stmt->close();
    return $count;
}

function getRecentWithdrawals(mysqli $conn, int $limit = 30): array {
    $stmt = $conn->prepare(
        "SELECT id, created_at
         FROM coin_withdrawals
         ORDER BY created_at DESC, id DESC
         LIMIT ?"
    );
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $withdrawals = [];
    $withdrawalIds = [];

    while ($row = $result->fetch_assoc()) {
        $id = (int) $row['id'];
        $withdrawalIds[] = $id;

        $withdrawals[$id] = [
            "id" => $id,
            "created_at" => date("M d, Y g:i A", strtotime($row['created_at'])),
            "items" => [],
            "total_amount" => 0,
        ];
    }

    $stmt->close();

    if (count($withdrawalIds) === 0) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($withdrawalIds), '?'));
    $types = str_repeat('i', count($withdrawalIds));

    $stmt2 = $conn->prepare(
        "SELECT withdrawal_id, coin_value, coin_count
         FROM coin_withdrawal_items
         WHERE withdrawal_id IN (" . $placeholders . ")
         ORDER BY withdrawal_id DESC"
    );

    // mysqli::bind_param needs references; use call_user_func_array for safety
    $bindParams = [];
    $bindParams[] = $types;

    for ($i = 0; $i < count($withdrawalIds); $i++) {
        $bindParams[] = &$withdrawalIds[$i];
    }

    $bindResult = call_user_func_array([$stmt2, 'bind_param'], $bindParams);
    if ($bindResult === false) {
        throw new Exception("Query failed: " . $stmt2->error);
    }

    $stmt2->execute();
    $result2 = $stmt2->get_result();

    while ($row = $result2->fetch_assoc()) {
        $wid = (int) $row['withdrawal_id'];
        $coin = (int) $row['coin_value'];
        $count = (int) $row['coin_count'];

        if (!isset($withdrawals[$wid])) continue;

        $withdrawals[$wid]['items'][] = [
            "coin_value" => $coin,
            "coin_count" => $count,
        ];

        $withdrawals[$wid]['total_amount'] += ($coin * $count);
    }

    $stmt2->close();

    // Preserve newest-first ordering by iterating original ids array
    $out = [];
    foreach ($withdrawalIds as $wid) {
        $out[] = $withdrawals[$wid];
    }

    return $out;
}

function getWithdrawalCount(mysqli $conn): int {
    $result = $conn->query("SELECT COUNT(*) AS cnt FROM coin_withdrawals");

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    return (int) $result->fetch_assoc()['cnt'];
}

function getTransactionsPage(mysqli $conn, int $perPage, int $offset, int $coinFilter = 0): array {
    if ($coinFilter > 0) {
        $stmt = $conn->prepare(
            "SELECT coin_value, created_at FROM transactions
             WHERE coin_value = ?
             ORDER BY created_at DESC, id DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bind_param("iii", $coinFilter, $perPage, $offset);
    } else {
        $stmt = $conn->prepare(
            "SELECT coin_value, created_at FROM transactions
             ORDER BY created_at DESC, id DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bind_param("ii", $perPage, $offset);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = [
            "coin_value" => (int) $row['coin_value'],
            "created_at" => date("M d, Y g:i A", strtotime($row['created_at'])),
        ];
    }

    $stmt->close();
    return $transactions;
}