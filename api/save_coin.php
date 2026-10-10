<?php
// api/save_coin.php
//
// Called by the ESP32: GET /alkansya/api/save_coin.php?coin=10

require_once "../config/database.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

$coin = intval($_GET['coin'] ?? 0);

if (!isValidCoin($coin)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid coin value"]);
    exit;
}

$success = insertTransaction($conn, $coin);

// The device made contact with the server either way — that's a separate
// fact from whether the database write succeeded, so we track it regardless.
markDeviceSeen($conn);

if ($success) {
    echo json_encode(["status" => "success", "coin" => $coin]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to save coin"]);
}

$conn->close();