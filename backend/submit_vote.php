<?php
require_once "config.php";
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $user_id = $input['user_id'] ?? null;
    $participant_id = $input['candidate_id'] ?? null; // renamed but still received from frontend
    $poll_id = $input['poll_id'] ?? null;

    if (!$user_id || !$participant_id || !$poll_id) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit;
    }

    // Verify user is logged in
    if (!isset($_SESSION['user']) || $_SESSION['user']['id'] != $user_id) {
        echo json_encode(["success" => false, "message" => "User not authenticated"]);
        exit;
    }

    // Check if user already voted in this poll
    $checkStmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM votes v 
        JOIN participants p ON v.participant_id = p.id 
        WHERE v.user_id = ? AND p.poll_id = ?
    ");
    $checkStmt->execute([$user_id, $poll_id]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(["success" => false, "message" => "You have already voted in this poll"]);
        exit;
    }

    // Verify participant exists and belongs to the poll
    $participantStmt = $conn->prepare("SELECT id FROM participants WHERE id = ? AND poll_id = ?");
    $participantStmt->execute([$participant_id, $poll_id]);
    
    if (!$participantStmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Invalid participant or poll"]);
        exit;
    }

    // Insert vote
    $voteStmt = $conn->prepare("INSERT INTO votes (user_id, participant_id, created_at) VALUES (?, ?, datetime('now'))");
    $voteStmt->execute([$user_id, $participant_id]);

    echo json_encode(["success" => true, "message" => "Vote submitted successfully"]);

} catch (Exception $e) {
    error_log("Submit vote error: " . $e->getMessage());
    echo json_encode([
        "success" => false, 
        "message" => "Failed to submit vote: " . $e->getMessage()
    ]);
}
exit;
?>