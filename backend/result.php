<?php
require 'db.php';
$dbPath = __DIR__ . '/voting.db';
$db = new PDO('sqlite:' . $dbPath);

// Enable exceptions for errors
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$poll_id = $_GET['poll_id'] ?? null;
if (!$poll_id) {
    echo json_encode(["status" => "error", "message" => "Poll ID missing"]);
    exit;
}

$stmt = $pdo->prepare("SELECT candidate, COUNT(*) as votes FROM votes WHERE poll_id=? GROUP BY candidate");
$stmt->execute([$poll_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
