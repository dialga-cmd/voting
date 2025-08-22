<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$input = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';

try {
    if ($action === 'submit') {
        $userId = $input['user_id'] ?? null;
        $pollId = $input['poll_id'] ?? null;
        $participantId = $input['participant_id'] ?? null;

        if (!$userId || !$pollId || !$participantId) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Check if user already voted in this poll
        $stmt = $conn->prepare("SELECT id FROM votes WHERE user_id = ? AND poll_id = ?");
        $stmt->execute([$userId, $pollId]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You have already voted in this poll']);
            exit;
        }

        // Verify that the participant belongs to this poll
        $stmt = $conn->prepare("SELECT name FROM participants WHERE id = ? AND poll_id = ?");
        $stmt->execute([$participantId, $pollId]);
        $participant = $stmt->fetch();
        if (!$participant) {
            echo json_encode(['success' => false, 'message' => 'Invalid candidate selection']);
            exit;
        }

        // Insert the vote
        $stmt = $conn->prepare("INSERT INTO votes (user_id, poll_id, participant_id) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $pollId, $participantId]);

        echo json_encode([
            'success' => true, 
            'message' => 'Vote submitted successfully',
            'voted_for' => $participant['name']
        ]);
        exit;
    }

    if ($action === 'check') {
        $userId = $input['user_id'] ?? null;
        $pollId = $input['poll_id'] ?? null;

        if (!$userId || !$pollId) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Check if user has voted
        $stmt = $conn->prepare("
            SELECT v.id, p.name as voted_candidate 
            FROM votes v 
            JOIN participants p ON v.participant_id = p.id 
            WHERE v.user_id = ? AND v.poll_id = ?
        ");
        $stmt->execute([$userId, $pollId]);
        $vote = $stmt->fetch();

        if ($vote) {
            echo json_encode([
                'success' => true,
                'hasVoted' => true,
                'votedCandidate' => $vote['voted_candidate']
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'hasVoted' => false
            ]);
        }
        exit;
    }

    if ($action === 'count') {
        // Get total vote count
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM votes");
        $stmt->execute();
        $result = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'total' => $result['total']
        ]);
        exit;
    }

    if ($action === 'results') {
        $pollId = $_GET['poll_id'] ?? null;
        
        if (!$pollId) {
            echo json_encode(['success' => false, 'message' => 'Poll ID required']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT p.name, COUNT(v.id) as vote_count
            FROM participants p
            LEFT JOIN votes v ON p.id = v.participant_id
            WHERE p.poll_id = ?
            GROUP BY p.id, p.name
            ORDER BY vote_count DESC
        ");
        $stmt->execute([$pollId]);
        $results = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'results' => $results
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (PDOException $e) {
    error_log("Database error in votes.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Error in votes.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>