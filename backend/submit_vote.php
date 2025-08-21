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
    $candidate_id = $input['candidate_id'] ?? null;
    $poll_id = $input['poll_id'] ?? null;

    if (!$user_id || !$candidate_id || !$poll_id) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit;
    }

    // Verify user is logged in
    if (!isset($_SESSION['user']) || $_SESSION['user']['id'] != $user_id) {
        echo json_encode(["success" => false, "message" => "User not authenticated"]);
        exit;
    }

    // Check if user has already voted in this poll
    $checkStmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM votes v 
        JOIN participants p ON v.candidate_id = p.id 
        WHERE v.user_id = ? AND p.poll_id = ?
    ");
    $checkStmt->execute([$user_id, $poll_id]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(["success" => false, "message" => "You have already voted in this poll"]);
        exit;
    }

    // Verify candidate exists and belongs to the correct poll
    $candidateStmt = $conn->prepare("SELECT id FROM participants WHERE id = ? AND poll_id = ?");
    $candidateStmt->execute([$candidate_id, $poll_id]);
    
    if (!$candidateStmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Invalid candidate or poll"]);
        exit;
    }

    // Begin transaction
    $conn->beginTransaction();

    try {
        // Insert the vote
        $voteStmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id, created_at) VALUES (?, ?, datetime('now'))");
        $voteStmt->execute([$user_id, $candidate_id]);

        // Increment candidate vote count (if candidates table has votes column)
        $updateStmt = $conn->prepare("UPDATE participants SET votes = votes + 1 WHERE id = ?");
        $updateStmt->execute([$candidate_id]);

        // Commit transaction
        $conn->commit();

        echo json_encode([
            "success" => true, 
            "message" => "Vote submitted successfully"
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Submit vote error: " . $e->getMessage());
    echo json_encode([
        "success" => false, 
        "message" => "Failed to submit vote: " . $e->getMessage()
    ]);
}
?>