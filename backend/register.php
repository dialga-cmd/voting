<?php
require_once "config.php";
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $username = trim($input["username"] ?? "");
    $password = trim($input["password"] ?? "");

    if (!$username || !$password) {
        echo json_encode(["success" => false, "message" => "Username and password required"]);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(["success" => false, "message" => "Password must be at least 6 characters long"]);
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
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, datetime('now'))");
    $stmt->execute([$username, $hashed, 'user']);

    // Get the newly created user ID
    $userId = $conn->lastInsertId();

    // Auto-login after signup
    $_SESSION["user"] = [
        "id" => $userId,
        "username" => $username,
        "role" => "user"
    ];

    echo json_encode(["success" => true, "username" => $username]);
} catch (Exception $e) {
    error_log("Register error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Registration failed. Please try again."]);
}
?>