<?php
require_once __DIR__ . '/config.php';
$db = $conn;
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        try {
            // Fetch all polls, newest first
            $stmt = $db->query("SELECT id, title, start_date, end_date FROM polls ORDER BY start_date DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'create':
        try {
            $title = trim($_POST['title'] ?? '');
            $start = $_POST['start_date'] ?? '';
            $end = $_POST['end_date'] ?? '';
            
            if (!$title) {
                echo json_encode(["status" => "error", "message" => "Title is required"]);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO polls (title, start_date, end_date) VALUES (?, ?, ?)");
            $success = $stmt->execute([$title, $start, $end]);
            
            if ($success) {
                $pollId = $db->lastInsertId();
                echo json_encode([
                    "status" => "success", 
                    "message" => "Poll created successfully",
                    "poll_id" => $pollId
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to create poll"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'delete':
        try {
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "Poll ID is required"]);
                exit;
            }

            // Begin transaction to handle cascading deletes
            $db->beginTransaction();

            try {
                // Delete votes first
                $db->prepare("DELETE FROM votes WHERE candidate_id IN (SELECT id FROM participants WHERE poll_id = ?)")->execute([$id]);
                
                // Delete participants
                $db->prepare("DELETE FROM participants WHERE poll_id = ?")->execute([$id]);
                
                // Delete candidates (if any)
                $db->prepare("DELETE FROM candidates WHERE poll_id = ?")->execute([$id]);
                
                // Delete poll
                $stmt = $db->prepare("DELETE FROM polls WHERE id = ?");
                $success = $stmt->execute([$id]);

                if ($success && $stmt->rowCount() > 0) {
                    $db->commit();
                    echo json_encode(["status" => "success", "message" => "Poll deleted successfully"]);
                } else {
                    $db->rollBack();
                    echo json_encode(["status" => "error", "message" => "Poll not found or already deleted"]);
                }

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'count':
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM polls");
            $count = $stmt->fetchColumn();
            echo json_encode(["total" => $count]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'get':
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "Poll ID is required"]);
                exit;
            }

            $stmt = $db->prepare("SELECT * FROM polls WHERE id = ?");
            $stmt->execute([$id]);
            $poll = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($poll) {
                echo json_encode(["status" => "success", "poll" => $poll]);
            } else {
                echo json_encode(["status" => "error", "message" => "Poll not found"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action: " . $action]);
}