<?php
// api/manual_insert.php
//
// Manual coin inserter (for development before hardware is available).
//
// Usage examples:
//   GET /alkansya/api/manual_insert.php?coin=5
//   GET /alkansya/api/manual_insert.php?coin=10&count=12
//
// This endpoint is NOT used by the UI.

require_once "../config/database.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

$coin  = intval($_GET['coin'] ?? 0);
$count = intval($_GET['count'] ?? 1);

if (!isValidCoin($coin)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid coin value" ]);
    $conn->close();
    exit;
}

if ($count < 1) $count = 1;

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO transactions (coin_value) VALUES (?)");
    $stmt->bind_param("i", $coin);

    $inserted = 0;
    for ($i = 0; $i < $count; $i++) {
        if ($stmt->execute()) {
            $inserted++;
        } else {
            throw new Exception("Insert failed");
        }
    }

    $stmt->close();

    // Mark device as seen so the dashboard shows device online.
    markDeviceSeen($conn);

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "coin" => $coin,
        "inserted" => $inserted,
    ]);
} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to insert coins" ]);
}

$conn->close();
