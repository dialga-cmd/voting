<?php
require_once "config.php";
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $username = trim($input["username"] ?? "");
    $password = trim($input["password"] ?? "");

    if (!$username || !$password) {
        echo json_encode(["success" => false, "message" => "Username and password required"]);
        exit;
    }

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Username already exists"]);
        exit;
    }

    // Insert new user
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashed]);

    // Auto-login after signup
    session_start();
    $_SESSION["user"] = [
        "id" => $conn->lastInsertId(),
        "username" => $username,
        "role" => "user"
    ];

    echo json_encode(["success" => true, "username" => $username]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
