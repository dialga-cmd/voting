<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email']);
$password = trim($data['password']);

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit;
}

// Hash password (more secure)
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);
    echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
}
?>
