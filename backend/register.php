<?php
require 'config.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);
    echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists or invalid']);
}
?>
