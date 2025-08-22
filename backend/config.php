<?php
$db_file = __DIR__ . "/voting.db";

try {
    $conn = new PDO("sqlite:$db_file");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Enable foreign key constraints
    $conn->exec("PRAGMA foreign_keys = ON;");
    
    // If database doesn't exist or is empty, redirect to setup
    $tables = $conn->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
    if (empty($tables)) {
        if (!headers_sent()) {
            header("Location: reset_db.php");
            exit;
        }
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]));
}
?>