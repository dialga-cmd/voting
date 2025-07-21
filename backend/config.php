<?php
$db_file = __DIR__ . "/voting.db";

try {
    $conn = new PDO("sqlite:$db_file");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables if they don't exist
    $conn->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE,
        password TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS candidates (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        votes INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS votes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        candidate_id INTEGER,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    ");

    // Insert sample candidates if table is empty
    $stmt = $conn->query("SELECT COUNT(*) FROM candidates");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("INSERT INTO candidates (name) VALUES ('John Smith'), ('Maria Johnson'), ('Alex Davis');");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
