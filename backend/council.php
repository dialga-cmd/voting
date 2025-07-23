<?php
require 'config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    if ($action === 'list') {
        $stmt = $conn->query("SELECT * FROM student_council ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($action === 'add') {
        $name = $_POST['name'] ?? '';
        $position = $_POST['position'] ?? '';

        $stmt = $conn->prepare("INSERT INTO student_council (name, position) VALUES (?, ?)");
        $stmt->execute([$name, $position]);
        echo json_encode(['status' => 'success', 'message' => 'Council member added']);
    }
    elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM student_council WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Council member removed']);
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
