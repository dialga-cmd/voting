<?php
session_start();
header("Content-Type: application/json");

try {
    $db = new PDO("sqlite:../voting.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $input = json_decode(file_get_contents("php://input"), true);
    $email = $input["email"] ?? "";
    $password = $input["password"] ?? "";

    if (!$email || !$password) {
        echo json_encode(["success" => false, "message" => "Email and password required"]);
        exit;
    }

    // Check if email already exists
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Email already registered"]);
        exit;
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashed]);

    echo json_encode(["success" => true, "message" => "Registration successful"]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Server error: " . $e->getMessage()
    ]);
}
