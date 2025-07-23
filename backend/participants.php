<?php
require 'config.php';
$db = $conn;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $stmt = $db->query("SELECT * FROM participants ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'add':
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $stmt = $db->prepare("INSERT INTO participants (name, email) VALUES (?, ?)");
        $success = $stmt->execute([$name, $email]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    case 'remove':
        $id = $_POST['id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM participants WHERE id=?");
        $success = $stmt->execute([$id]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
