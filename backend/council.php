<?php
require_once __DIR__ . '/config.php';
$db = $conn;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $stmt = $db->query("SELECT id, name, position FROM student_council ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'add':
        $name = $_POST['name'] ?? '';
        $position = $_POST['position'] ?? '';
        if (!$name || !$position) {
            echo json_encode(["status" => "error", "message" => "Name and position required"]);
            exit;
        }
        $stmt = $db->prepare("INSERT INTO student_council (name, position) VALUES (?, ?)");
        $success = $stmt->execute([$name, $position]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM student_council WHERE id=?");
        $success = $stmt->execute([$id]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
