<?php
require_once "config.php";
header('Content-Type: application/json');
session_start();

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $username = trim($input["username"] ?? "");
    $password = trim($input["password"] ?? "");

    if (!$username || !$password) {
        echo json_encode(["success" => false, "message" => "Username and password required"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = [
            "id" => $user["id"],
            "username" => $user["username"],
            "role" => $user["role"]
        ];
        echo json_encode(["success" => true, "username" => $user["username"]]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid username or password"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
