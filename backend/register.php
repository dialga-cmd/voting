<?php
require_once "config.php";
session_start();
header("Content-Type: application/json");

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
if (!is_array($data)) { $data = $_POST; }

$username = trim($data["username"] ?? "");
$password = $data["password"] ?? "";

if (!$username || !$password) {
    echo json_encode(["success" => false, "message" => "Username and password are required."]);
    exit;
}

try {
    // check duplicate
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Username already taken."]);
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    $stmt->execute([$username, $hash]);

    // auto-login
    $userId = $conn->lastInsertId();
    $_SESSION["user_id"] = $userId;
    $_SESSION["username"] = $username;
    $_SESSION["role"] = "user";

    echo json_encode(["success" => true, "username" => $username]);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Server error: ".$e->getMessage()]);
}
