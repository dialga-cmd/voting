<?php
require_once "config.php";
session_start();

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

if (empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Missing username or password."]);
    exit;
}

try {
    // Check if username already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(["success" => false, "message" => "Username already taken."]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);

    $userId = $conn->lastInsertId();

    // Auto-login after registration
    $_SESSION["user_id"] = $userId;
    $_SESSION["username"] = $username;
    $_SESSION["role"] = "user";

    echo json_encode(["success" => true, "username" => $username]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
