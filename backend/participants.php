<?php
require 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $poll_id = $_GET['poll_id'];
        $stmt = $pdo->prepare("SELECT id, name, email FROM participants WHERE poll_id=?");
        $stmt->execute([$poll_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'add':
        $poll_id = $_POST['poll_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];

        $stmt = $pdo->prepare("INSERT INTO participants (poll_id, name, email) VALUES (?, ?, ?)");
        $success = $stmt->execute([$poll_id, $name, $email]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    case 'remove':
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM participants WHERE id=?");
        $success = $stmt->execute([$id]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
