<?php
require_once "config.php";
session_start();
header("Content-Type: application/json");

if (isset($_SESSION["user_id"])) {
    echo json_encode([
        "loggedIn" => true,
        "email" => $_SESSION["email"],
        "role" => $_SESSION["role"]
    ]);
} else {
    echo json_encode(["loggedIn" => false]);
}