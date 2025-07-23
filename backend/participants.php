<?php
require 'config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    if ($action === 'list') {
        $poll_id = $_GET['poll_id'] ?? 0;
        $stmt = $conn->prepare("SELECT * FROM participants WHERE poll_id = ?");
        $stmt->execute([$poll_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($action === 'add') {
        $poll_id = $_POST['poll_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';

        $stmt = $conn->prepare("INSERT INTO participants (poll_id, name, email) VALUES (?, ?, ?)");
        $stmt->execute([$poll_id, $name, $email]);
        echo json_encode(['status' => 'success', 'message' => 'Participant added']);
    }
    elseif ($action === 'remove') {
        $id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM participants WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Participant removed']);
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
