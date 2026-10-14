<?php
// api/withdraw.php
//
// Multi-denomination coin withdrawals.
// Actions:
//   GET  ?action=state  -> remaining counts + recent withdrawal history
//   POST ?action=submit -> withdraw selected coins (multi-denomination)

require_once "../config/database.php";
require_once "../includes/functions.php";

header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

switch ($action) {

    // ── Fetch remaining + recent history ──
    case 'state':
        try {
            $remainingCounts = getCoinCounts($conn); // net remaining counts
            $remainingTotal   = getTotalSavings($conn);
            $remainingCoins   = getTotalCoinCount($conn);

            $withdrawals = getRecentWithdrawals($conn, 100);

            echo json_encode([
                "status" => "success",
                "remaining" => [
                    "counts" => $remainingCounts,
                    "total" => $remainingTotal,
                    "total_coins" => $remainingCoins,
                ],
                "withdrawals" => $withdrawals,
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
        break;

    // ── Submit a withdrawal ──
    case 'submit':
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Method not allowed"]);
            break;
        }

        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true);

        $items = $data['items'] ?? [];
        if (!is_array($items) || count($items) === 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "No withdrawal items provided"]);
            break;
        }

        // Normalize & validate items
        $normalized = [];
        foreach ($items as $it) {
            $coin = isset($it['coin_value']) ? intval($it['coin_value']) : 0;
            $cnt  = isset($it['coin_count']) ? intval($it['coin_count']) : 0;

            if (!isValidCoin($coin)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Invalid coin value"]);
                break 2;
            }

            if ($cnt <= 0) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "coin_count must be >= 1"]);
                break 2;
            }

            // Aggregate same denomination (so UI can send duplicates safely)
            if (!isset($normalized[$coin])) $normalized[$coin] = 0;
            $normalized[$coin] += $cnt;
        }

        $normalizedItems = [];
        foreach ($normalized as $coin => $cnt) {
            if ($cnt > 0) {
                $normalizedItems[] = [
                    "coin_value" => (int) $coin,
                    "coin_count" => (int) $cnt,
                ];
            }
        }

        if (count($normalizedItems) === 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Nothing to withdraw"]);
            break;
        }

        // Validate against remaining inventory
        $remainingCounts = getCoinCounts($conn);
        foreach ($normalizedItems as $it) {
            $coin = $it['coin_value'];
            $cnt  = $it['coin_count'];

            $available = $remainingCounts[$coin] ?? 0;
            if ($cnt > $available) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "Not enough coins for ₱" . $coin . ". Available: " . $available
                ]);
                break 2;
            }
        }

        // Persist withdrawal as header + item rows
        $conn->begin_transaction();

        try {
            $stmtHeader = $conn->prepare("INSERT INTO coin_withdrawals () VALUES ()");
            $stmtHeader->execute();
            $withdrawalId = $stmtHeader->insert_id;
            $stmtHeader->close();

            $stmtItem = $conn->prepare(
                "INSERT INTO coin_withdrawal_items (withdrawal_id, coin_value, coin_count) VALUES (?, ?, ?)"
            );

            foreach ($normalizedItems as $it) {
                $coin = $it['coin_value'];
                $cnt  = $it['coin_count'];

                $stmtItem->bind_param("iii", $withdrawalId, $coin, $cnt);
                $stmtItem->execute();
            }

            $stmtItem->close();
            $conn->commit();

            echo json_encode([
                "status" => "success",
                "withdrawal_id" => (int) $withdrawalId,
            ]);

        } catch (Throwable $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Withdrawal failed"]);
        }

        break;

    default:
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Unknown action"]);
}

$conn->close();
