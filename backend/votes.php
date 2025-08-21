<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "You must log in first"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participants_id = intval($_POST['participants_id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id FROM votes WHERE user_id=?");
    $stmt->execute([$user_id]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "You already voted"]);
        exit;
    }

    $conn->beginTransaction();
    try {
        $stmt = $conn->prepare("INSERT INTO votes (user_id, participants_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $participants_id]);

        $stmt = $conn->prepare("UPDATE candidates SET votes = votes + 1 WHERE id=?");
        $stmt->execute([$participants_id]);

        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Vote submitted"]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => "Vote failed"]);
    }
}
?>
