<?php
// api/heartbeat.php
//
// Called by the ESP32 every ~15 seconds, just to prove it's still alive
// even when no coin has been inserted recently.

require_once "../config/database.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

markDeviceSeen($conn);

echo json_encode(["status" => "ok"]);

$conn->close();