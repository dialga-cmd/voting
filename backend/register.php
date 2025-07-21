<?php
session_start();
require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 4) {
        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
        exit;
    }

    try {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $hashed]);
        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "User already exists"]);
    }
}
?>
