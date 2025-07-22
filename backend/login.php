<?php
require "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email']);
$password = trim($data['password']);

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'success', 'email' => $user['email']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}
?>
