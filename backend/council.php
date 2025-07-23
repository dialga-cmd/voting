<?php
require 'config.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $conn->query("SELECT id, name, position FROM student_council ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    elseif ($action === 'add') {
        $name = $_POST['name'];
        $position = $_POST['position'];
        $stmt = $conn->prepare("INSERT INTO student_council (name, position) VALUES (?, ?)");
        $stmt->execute([$name, $position]);
        echo json_encode(["status" => "success", "message" => "Member added successfully."]);
    }

    elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM student_council WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "success", "message" => "Member removed successfully."]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
