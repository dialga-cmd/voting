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
    echo json_encode(["success" => false, "message" => "Username and password required."]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        echo json_encode(["success" => false, "message" => "Either username or password is wrong"]);
        exit;
    }

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];

    echo json_encode([
        "success" => true,
        "username" => $_SESSION["username"],
        "role" => $_SESSION["role"]
    ]);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Server error: ".$e->getMessage()]);
}
