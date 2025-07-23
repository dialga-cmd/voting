<?php
require_once __DIR__ . '/config.php';
$db = $conn;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        // Fetch all users with role 'user' (participants)
        $stmt = $db->query("SELECT id, name, email FROM users WHERE role='user' ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'add':
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        // email and name required
        if (!$name || !$email) {
            echo json_encode(["status" => "error", "message" => "Name and email required"]);
            exit;
        }
        $stmt = $db->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, 'user')");
        $success = $stmt->execute([$name, $email]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    case 'remove':
        $id = $_POST['id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM users WHERE id=? AND role='user'");
        $success = $stmt->execute([$id]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
