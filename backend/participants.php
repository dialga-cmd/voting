<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
$db = $conn;
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $poll_id = $_GET['poll_id'] ?? null;
        if ($poll_id) {
            $stmt = $db->prepare("SELECT id, name, email FROM users WHERE role = 'user' AND poll_id = ?");
            $stmt->execute([$poll_id]);
        } else {
            $stmt = $db->query("SELECT id, name, email FROM users WHERE role = 'user'");
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'add':
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $poll_id = $_POST['poll_id'] ?? '';

        if (!$name || !$email || !$poll_id) {
            echo json_encode(["status" => "error", "message" => "All fields required"]);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO users (name, email, role, poll_id) VALUES (?, ?, 'user', ?)");
        $saved = $stmt->execute([$name, $email, $poll_id]);

        echo json_encode(["status" => $saved ? "success" : "error"]);
        break;

    case 'remove':
        $id = $_POST['id'] ?? 0;

        // Remove all votes for this user
        $db->prepare("DELETE FROM votes WHERE user_id = ?")->execute([$id]);

        // Remove the user
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $deleted = $stmt->execute([$id]);

        echo json_encode(["status" => $deleted ? "success" : "error"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}