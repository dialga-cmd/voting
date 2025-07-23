<?php
require 'config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    if ($action === 'list') {
        $stmt = $conn->query("SELECT * FROM polls ORDER BY start_date DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($action === 'create') {
        $title = $_POST['title'] ?? '';
        $start = $_POST['start_date'] ?? '';
        $end = $_POST['end_date'] ?? '';

        $stmt = $conn->prepare("INSERT INTO polls (title, start_date, end_date) VALUES (?, ?, ?)");
        $stmt->execute([$title, $start, $end]);
        echo json_encode(['status' => 'success', 'message' => 'Poll created successfully']);
    }
    elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM polls WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Poll deleted successfully']);
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
