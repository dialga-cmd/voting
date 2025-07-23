<?php
require 'config.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $poll_id = $_GET['poll_id'] ?? null;
        if (!$poll_id) {
            echo json_encode(["status" => "error", "message" => "Poll ID required."]);
            exit;
        }
        $stmt = $conn->prepare("SELECT id, email FROM users WHERE id IN (SELECT user_id FROM votes WHERE poll_id = ?)");
        $stmt->execute([$poll_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    elseif ($action === 'add') {
        $poll_id = $_POST['poll_id'];
        $email = $_POST['email'];
        $password = password_hash('default123', PASSWORD_BCRYPT);

        // Add user if not exists
        $stmt = $conn->prepare("INSERT OR IGNORE INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $password]);

        echo json_encode(["status" => "success", "message" => "Participant added successfully."]);
    }

    elseif ($action === 'remove') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "success", "message" => "Participant removed successfully."]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
