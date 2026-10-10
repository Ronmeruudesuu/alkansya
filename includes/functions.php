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
        die("Query failed: " . $conn->error);
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
    $result = $conn->query("SELECT COALESCE(SUM(coin_value), 0) AS total FROM transactions");

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return (float) $result->fetch_assoc()['total'];
}

function getTotalCoinCount(mysqli $conn): int {
    $result = $conn->query("SELECT COUNT(*) AS cnt FROM transactions");

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    return (int) $result->fetch_assoc()['cnt'];
}

function getCoinCounts(mysqli $conn): array {
    $counts = [];

    foreach ([1, 5, 10, 20] as $value) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM transactions WHERE coin_value = ?");
        $stmt->bind_param("i", $value);
        $stmt->execute();
        $counts[$value] = (int) $stmt->get_result()->fetch_assoc()['cnt'];
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