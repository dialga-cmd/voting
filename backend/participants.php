<?php
require_once "config.php";
header("Content-Type: application/json");

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'list') {
    try {
        $poll_id = $_GET['poll_id'] ?? null;

        if ($poll_id) {
            $stmt = $conn->prepare("SELECT * FROM participants WHERE poll_id = ? ORDER BY name");
            $stmt->execute([$poll_id]);
        } else {
            $stmt = $conn->query("SELECT * FROM participants ORDER BY name");
        }
        
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($participants);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}

elseif ($action === 'add') {
    try {
        // Handle both JSON and form data
        if ($_SERVER['CONTENT_TYPE'] === 'application/json' || strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $_POST;
        }

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $poll_id = $data['poll_id'] ?? null;

        if (!$name || !$email || !$poll_id) {
            echo json_encode(["status" => "error", "message" => "Name, email and poll_id are required"]);
            exit;
        }

        // Check if poll exists
        $pollCheck = $conn->prepare("SELECT id FROM polls WHERE id = ?");
        $pollCheck->execute([$poll_id]);
        if (!$pollCheck->fetch()) {
            echo json_encode(["status" => "error", "message" => "Invalid poll ID"]);
            exit;
        }

        // Check if participant already exists for this poll
        $existsCheck = $conn->prepare("SELECT id FROM participants WHERE email = ? AND poll_id = ?");
        $existsCheck->execute([$email, $poll_id]);
        if ($existsCheck->fetch()) {
            echo json_encode(["status" => "error", "message" => "Participant with this email already exists for this poll"]);
            exit;
        }

        // Insert participant
        $stmt = $conn->prepare("INSERT INTO participants (name, email, poll_id) VALUES (?, ?, ?)");
        $success = $stmt->execute([$name, $email, $poll_id]);
        
        if ($success) {
            echo json_encode(["status" => "success", "message" => "Participant added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add participant"]);
        }
        
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

elseif ($action === 'remove' || $action === 'delete') {
    try {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Participant ID is required"]);
            exit;
        }

        // Begin transaction to delete participant and their votes
        $conn->beginTransaction();

        try {
            // Delete votes first (due to foreign key constraints)
            $deleteVotes = $conn->prepare("DELETE FROM votes WHERE candidate_id = ?");
            $deleteVotes->execute([$id]);

            // Delete participant
            $deleteParticipant = $conn->prepare("DELETE FROM participants WHERE id = ?");
            $success = $deleteParticipant->execute([$id]);

            if ($success && $deleteParticipant->rowCount() > 0) {
                $conn->commit();
                echo json_encode(["status" => "success", "message" => "Participant removed successfully"]);
            } else {
                $conn->rollBack();
                echo json_encode(["status" => "error", "message" => "Participant not found or already deleted"]);
            }

        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

elseif ($action === 'count') {
    try {
        $stmt = $conn->query("SELECT COUNT(*) FROM participants");
        $count = $stmt->fetchColumn();
        echo json_encode(["total" => $count]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

elseif ($action === 'vote') {
    // This is handled by submit_vote.php now, but keeping for backward compatibility
    echo json_encode(["success" => false, "message" => "Use submit_vote.php for voting"]);
}

else {
    echo json_encode(["status" => "error", "message" => "Invalid action: " . $action]);
}
?>