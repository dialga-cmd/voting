<?php
require 'config.php';
header('Content-Type: application/json');

$poll_id = $_GET['poll_id'] ?? 0;

try {
    $stmt = $conn->prepare("
        SELECT candidates.name, candidates.votes
        FROM candidates
        WHERE candidates.poll_id = ?
        ORDER BY votes DESC
    ");
    $stmt->execute([$poll_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
