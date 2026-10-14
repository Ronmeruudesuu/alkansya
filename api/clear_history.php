<?php
// api/clear_history.php
//
// Clears ALL history of:
//  - inserted coins   (transactions table)
//  - withdrawals       (coin_withdrawals + coin_withdrawal_items tables)
//
// Endpoint:
//   POST /alkansya/api/clear_history.php
//   body: { "action": "clear_all" }
//
// Returns: { status: "success" } on success.

require_once "../config/database.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    $conn->close();
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$action = $data["action"] ?? '';

if ($action !== 'clear_all') {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Unknown action"]);
    $conn->close();
    exit;
}

$conn->begin_transaction();

try {
    // Delete detail rows first (extra safe)
    $conn->query("DELETE FROM coin_withdrawal_items");

    // Delete withdrawal headers
    $conn->query("DELETE FROM coin_withdrawals");

    // Delete insert history
    $conn->query("DELETE FROM transactions");

    $conn->commit();

    echo json_encode(["status" => "success"]);
} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to clear history"]);
}

$conn->close();
