<?php
require 'config.php';

header('Content-Type: application/json');
$poll_id = $_GET['poll_id'] ?? null;

try {
    if ($poll_id) {
        $stmt = $conn->prepare("
            SELECT c.name AS candidate, c.votes 
            FROM candidates c 
            WHERE c.poll_id = ? 
            ORDER BY c.votes DESC
        ");
        $stmt->execute([$poll_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } else {
        // List results for all ended polls
        $stmt = $conn->query("
            SELECT p.id, p.title, p.end_date,
                (SELECT name FROM candidates WHERE poll_id = p.id ORDER BY votes DESC LIMIT 1) AS winner,
                (SELECT MAX(votes) FROM candidates WHERE poll_id = p.id) AS max_votes
            FROM polls p
            WHERE p.end_date < DATE('now')
            ORDER BY p.end_date DESC
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
