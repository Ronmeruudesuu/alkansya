<?php
// api/correct_last_coin.php
//
// Lets the dashboard correct the *latest* inserted transaction coin_value.
// Called by the website when user clicks "Wrong coin inserted?".

require_once "../config/database.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    $conn->close();
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$coin = isset($data["coin"]) ? intval($data["coin"]) : 0;

if (!isValidCoin($coin)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid coin value"]);
    $conn->close();
    exit;
}

// Block correction when the vault has no remaining coins (e.g., user withdrew everything)
$remaining = getTotalCoinCount($conn);
if ($remaining <= 0) {
    http_response_code(409);
    echo json_encode([
        "status" => "error",
        "message" => "Vault is empty — cannot correct last insert."
    ]);
    $conn->close();
    exit;
}

// Get the latest transaction (most recent globally)
$stmt = $conn->prepare("SELECT id, coin_value FROM transactions ORDER BY id DESC LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result === false || $result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "No transactions found to correct"]);
    $stmt->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$txId = (int) $row["id"];
$oldCoin = (int) $row["coin_value"];

$stmt->close();

if ($oldCoin === $coin) {
    echo json_encode([
        "status" => "success",
        "old_coin" => $oldCoin,
        "new_coin" => $coin,
        "message" => "No change needed",
    ]);

    $conn->close();
    exit;
}

$update = $conn->prepare("UPDATE transactions SET coin_value = ? WHERE id = ?");
$update->bind_param("ii", $coin, $txId);
$ok = $update->execute();

$update->close();

if (!$ok) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to correct last coin"]);
    $conn->close();
    exit;
}

echo json_encode([
    "status" => "success",
    "old_coin" => $oldCoin,
    "new_coin" => $coin,
]);

$conn->close();
?>
