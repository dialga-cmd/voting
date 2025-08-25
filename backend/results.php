<?php
header("Content-Type: application/json");
require_once "db.php"; // your SQLite connection

$action = $_GET['action'] ?? '';

if ($action === 'completed') {
    $stmt = $db->query("SELECT id, title, start_date, end_date FROM polls WHERE status = 'completed'");
    $polls = [];

    while ($poll = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $poll_id = $poll['id'];

        // Total votes
        $voteStmt = $db->prepare("SELECT COUNT(*) as total FROM votes WHERE poll_id = ?");
        $voteStmt->execute([$poll_id]);
        $totalVotes = $voteStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Options + votes
        $optStmt = $db->prepare("SELECT option_text, COUNT(votes.id) as votes 
                                FROM options 
                                LEFT JOIN votes ON options.id = votes.option_id 
                                WHERE options.poll_id = ? 
                                GROUP BY options.id");
        $optStmt->execute([$poll_id]);
        $options = $optStmt->fetchAll(PDO::FETCH_ASSOC);

        $polls[] = [
            "id" => $poll_id,
            "title" => $poll['title'],
            "start_date" => $poll['start_date'],
            "end_date" => $poll['end_date'],
            "total_votes" => $totalVotes,
            "options" => $options
        ];
    }

    echo json_encode($polls);
    exit;
}

echo json_encode(["error" => "Invalid action"]);
?>