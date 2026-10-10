<?php
// api/get_data.php
//
// Called by the dashboard's JavaScript, on a timer, to get the latest
// numbers. Everything the UI needs comes back as one JSON object.

require_once "../config/database.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

$deviceStatus = getDeviceStatus($conn);

echo json_encode([
    "total"         => getTotalSavings($conn),
    "total_coins"   => getTotalCoinCount($conn),
    "counts"        => getCoinCounts($conn),
    "device_online" => $deviceStatus["online"],
    "last_seen"     => $deviceStatus["last_seen"],
    "recent"        => getRecentTransactions($conn),
]);

$conn->close();