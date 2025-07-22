<?php
require 'db.php'; // Uses $pdo from db.php
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $stmt = $pdo->query("SELECT id, name, role FROM student_council ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'add':
        $name = $_POST['name'];
        $role = $_POST['role'];
        $stmt = $pdo->prepare("INSERT INTO student_council (name, role) VALUES (?, ?)");
        $success = $stmt->execute([$name, $role]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    case 'delete':
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM student_council WHERE id=?");
        $success = $stmt->execute([$id]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
