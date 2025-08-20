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
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        echo json_encode(["success" => true, "username" => $user["username"]]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid username or password."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
