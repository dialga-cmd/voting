<?php
require_once "config.php";
session_start();

header("Content-Type: application/json");

if (isset($_SESSION["user_id"])) {
    echo json_encode([
        "success"  => true,
        "id"       => $_SESSION["user_id"],
        "username" => $_SESSION["username"],
        "role"     => $_SESSION["role"]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Not logged in"
    ]);
}
