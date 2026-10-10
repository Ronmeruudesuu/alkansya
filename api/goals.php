<?php
// api/goals.php
//
// CRUD API for savings goals (pag-iipunan).
// Supports: ?action=list | add | update | delete

require_once "../config/database.php";

header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

switch ($action) {

    // ── LIST all goals ──
    case 'list':
        $result = $conn->query("SELECT id, name, target_amount, created_at FROM savings_goals ORDER BY created_at DESC");
        $goals = [];
        while ($row = $result->fetch_assoc()) {
            $goals[] = [
                "id"            => (int) $row['id'],
                "name"          => $row['name'],
                "target_amount" => (float) $row['target_amount'],
                "created_at"    => date("M d, Y", strtotime($row['created_at'])),
            ];
        }
        echo json_encode($goals);
        break;

    // ── ADD a new goal ──
    case 'add':
        $data = json_decode(file_get_contents("php://input"), true);
        $name   = trim($data['name'] ?? '');
        $target = floatval($data['target_amount'] ?? 0);

        if ($name === '' || $target <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid name or target amount"]);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO savings_goals (name, target_amount) VALUES (?, ?)");
        $stmt->bind_param("sd", $name, $target);
        $stmt->execute();
        echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        $stmt->close();
        break;

    // ── UPDATE an existing goal ──
    case 'update':
        $data   = json_decode(file_get_contents("php://input"), true);
        $id     = intval($data['id'] ?? 0);
        $name   = trim($data['name'] ?? '');
        $target = floatval($data['target_amount'] ?? 0);

        if ($id <= 0 || $name === '' || $target <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid data"]);
            break;
        }

        $stmt = $conn->prepare("UPDATE savings_goals SET name = ?, target_amount = ? WHERE id = ?");
        $stmt->bind_param("sdi", $name, $target, $id);
        $stmt->execute();
        echo json_encode(["success" => true]);
        $stmt->close();
        break;

    // ── DELETE a goal ──
    case 'delete':
        $data = json_decode(file_get_contents("php://input"), true);
        $id   = intval($data['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid ID"]);
            break;
        }

        $stmt = $conn->prepare("DELETE FROM savings_goals WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["success" => true]);
        $stmt->close();
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Unknown action"]);
}

$conn->close();
