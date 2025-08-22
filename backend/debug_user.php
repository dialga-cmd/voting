<?php
// Create this as backend/debug_user.php
require_once "config.php";
session_start();
header('Content-Type: text/plain');

echo "=== USER DEBUG INFO ===\n\n";

echo "Session data:\n";
print_r($_SESSION);

echo "\n=== ALL USERS IN DATABASE ===\n";
$result = $conn->query("SELECT id, username, role, created_at FROM users");
$users = $result->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    echo "ID: {$user['id']} | Username: {$user['username']} | Role: {$user['role']} | Created: {$user['created_at']}\n";
}

echo "\n=== ALL PARTICIPANTS ===\n";
$result = $conn->query("SELECT id, name, email, poll_id FROM participants");
$participants = $result->fetchAll(PDO::FETCH_ASSOC);

foreach ($participants as $participant) {
    echo "ID: {$participant['id']} | Name: {$participant['name']} | Email: {$participant['email']} | Poll ID: {$participant['poll_id']}\n";
}

echo "\n=== TEST LOGIN ===\n";
// Test if testuser exists and password works
$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
$stmt->execute(['testuser']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "Test user found: ID {$user['id']}, Username: {$user['username']}\n";
    $passwordCheck = password_verify('test123', $user['password']);
    echo "Password verification: " . ($passwordCheck ? "SUCCESS" : "FAILED") . "\n";
} else {
    echo "Test user NOT found\n";
}
?>