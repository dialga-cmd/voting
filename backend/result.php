<?php
require 'db.php';

$poll_id = $_GET['poll_id'] ?? null;
if (!$poll_id) {
    echo json_encode(["status" => "error", "message" => "Poll ID missing"]);
    exit;
}

$stmt = $pdo->prepare("SELECT candidate, COUNT(*) as votes FROM votes WHERE poll_id=? GROUP BY candidate");
$stmt->execute([$poll_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
