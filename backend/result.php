<?php
require_once __DIR__ . '/config.php';
$db = $conn;

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $stmt = $db->query("
        SELECT polls.title AS poll_title, candidates.name AS candidate, candidates.votes
        FROM polls
        LEFT JOIN candidates ON candidates.poll_id = polls.id
        ORDER BY polls.id DESC
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode(["status" => "error", "message" => "Invalid action"]);
}