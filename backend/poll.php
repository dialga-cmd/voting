<?php
require 'db.php'; // Uses $pdo from db.php
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $stmt = $pdo->query("SELECT id, title, start_date, end_date, candidates FROM polls ORDER BY start_date DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'create':
        $title = $_POST['title'];
        $start = $_POST['start_date'];
        $end = $_POST['end_date'];
        $candidates = $_POST['candidates'];

        $stmt = $pdo->prepare("INSERT INTO polls (title, start_date, end_date, candidates) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$title, $start, $end, $candidates]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    case 'update':
        $id = $_POST['id'];
        $title = $_POST['title'];
        $start = $_POST['start_date'];
        $end = $_POST['end_date'];
        $candidates = $_POST['candidates'];

        $stmt = $pdo->prepare("UPDATE polls SET title=?, start_date=?, end_date=?, candidates=? WHERE id=?");
        $success = $stmt->execute([$title, $start, $end, $candidates, $id]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    case 'delete':
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM polls WHERE id=?");
        $success = $stmt->execute([$id]);
        echo json_encode(["status" => $success ? "success" : "error"]);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
