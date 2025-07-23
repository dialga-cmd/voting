<?php
require 'config.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

try {
    if ($action === 'list') {
        // List all polls with their status (Ongoing/Upcoming/Ended)
        $stmt = $conn->query("SELECT id, title, start_date, end_date FROM polls ORDER BY start_date DESC");
        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $currentDate = date('Y-m-d');
        foreach ($polls as &$poll) {
            if ($currentDate < $poll['start_date']) {
                $poll['status'] = 'Upcoming';
            } elseif ($currentDate > $poll['end_date']) {
                $poll['status'] = 'Ended';
            } else {
                $poll['status'] = 'Ongoing';
            }
        }
        echo json_encode($polls);
    }

    elseif ($action === 'create') {
        $title = $_POST['title'];
        $start = $_POST['start_date'];
        $end = $_POST['end_date'];

        $stmt = $conn->prepare("INSERT INTO polls (title, start_date, end_date) VALUES (?, ?, ?)");
        $stmt->execute([$title, $start, $end]);
        echo json_encode(["status" => "success", "message" => "Poll created successfully."]);
    }

    elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM polls WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "success", "message" => "Poll deleted successfully."]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
