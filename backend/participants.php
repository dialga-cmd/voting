<?php
require_once "config.php";

header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    try {
        $poll_id = $_GET['poll_id'] ?? null;

        if ($poll_id) {
            $stmt = $conn->prepare("SELECT * FROM participants WHERE poll_id = ?");
            $stmt->execute([$poll_id]);
            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // fallback: return all if poll_id not specified
            $stmt = $conn->query("SELECT * FROM participants");
            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($participants);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

elseif ($action === 'add') {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $poll_id = $data['poll_id'] ?? null;

    if ($name && $email && $poll_id) {
        try {
            $stmt = $conn->prepare("INSERT INTO participants (name, email, poll_id) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $poll_id]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Missing fields"]);
    }
}

elseif ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        try {
            $stmt = $conn->prepare("DELETE FROM participants WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID missing"]);
    }
}

else {
    echo json_encode(["success" => false, "message" => "Invalid action"]);
}