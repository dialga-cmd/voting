<?php
require_once __DIR__ . '/config.php';
$db = $conn;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        // Fetch all polls, newest first
        $stmt = $db->query("SELECT id, title, start_date, end_date FROM polls ORDER BY start_date DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'create':
        $title = $_POST['title'] ?? '';
        $start = $_POST['start_date'] ?? '';
        $end = $_POST['end_date'] ?? '';
        if (!$title) {
            echo json_encode(["status" => "error", "message" => "Title required"]);
            exit;
        }
        $stmt = $db->prepare("INSERT INTO polls (title, start_date, end_date) VALUES (?, ?, ?)");
        $success = $stmt->execute([$title, $start, $end]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        $stmt = $db->prepare("DELETE FROM polls WHERE id=?");
        $success = $stmt->execute([$id]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
