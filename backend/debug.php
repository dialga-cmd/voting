<?php
// Create this as backend/debug_db.php
require_once "config.php";
header('Content-Type: text/plain');

echo "=== DATABASE SCHEMA DEBUG ===\n\n";

try {
    // Check if database file exists
    $db_file = __DIR__ . "/voting.db";
    echo "Database file: $db_file\n";
    echo "File exists: " . (file_exists($db_file) ? "YES" : "NO") . "\n\n";

    // Check votes table structure
    echo "=== VOTES TABLE STRUCTURE ===\n";
    $result = $conn->query("PRAGMA table_info(votes)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "Column: {$column['name']} | Type: {$column['type']} | NotNull: {$column['notnull']} | Default: {$column['dflt_value']}\n";
    }
    
    echo "\n=== VOTES TABLE FOREIGN KEYS ===\n";
    $result = $conn->query("PRAGMA foreign_key_list(votes)");
    $fks = $result->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($fks as $fk) {
        echo "From: {$fk['from']} -> To: {$fk['table']}.{$fk['to']}\n";
    }

    echo "\n=== PARTICIPANTS TABLE STRUCTURE ===\n";
    $result = $conn->query("PRAGMA table_info(participants)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "Column: {$column['name']} | Type: {$column['type']} | NotNull: {$column['notnull']} | Default: {$column['dflt_value']}\n";
    }

    echo "\n=== USERS TABLE STRUCTURE ===\n";
    $result = $conn->query("PRAGMA table_info(users)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "Column: {$column['name']} | Type: {$column['type']} | NotNull: {$column['notnull']} | Default: {$column['dflt_value']}\n";
    }

    echo "\n=== DATA CHECK ===\n";
    
    // Check users
    $result = $conn->query("SELECT COUNT(*) FROM users");
    echo "Users count: " . $result->fetchColumn() . "\n";
    
    $result = $conn->query("SELECT id, username, role FROM users LIMIT 5");
    $users = $result->fetchAll(PDO::FETCH_ASSOC);
    echo "Sample users:\n";
    foreach ($users as $user) {
        echo "  ID: {$user['id']}, Username: {$user['username']}, Role: {$user['role']}\n";
    }
    
    // Check participants
    $result = $conn->query("SELECT COUNT(*) FROM participants");
    echo "\nParticipants count: " . $result->fetchColumn() . "\n";
    
    $result = $conn->query("SELECT id, name, poll_id FROM participants LIMIT 5");
    $participants = $result->fetchAll(PDO::FETCH_ASSOC);
    echo "Sample participants:\n";
    foreach ($participants as $participant) {
        echo "  ID: {$participant['id']}, Name: {$participant['name']}, Poll ID: {$participant['poll_id']}\n";
    }
    
    // Check polls
    $result = $conn->query("SELECT COUNT(*) FROM polls");
    echo "\nPolls count: " . $result->fetchColumn() . "\n";
    
    $result = $conn->query("SELECT id, title FROM polls LIMIT 5");
    $polls = $result->fetchAll(PDO::FETCH_ASSOC);
    echo "Sample polls:\n";
    foreach ($polls as $poll) {
        echo "  ID: {$poll['id']}, Title: {$poll['title']}\n";
    }
    
    // Check votes
    $result = $conn->query("SELECT COUNT(*) FROM votes");
    echo "\nVotes count: " . $result->fetchColumn() . "\n";

    echo "\n=== FOREIGN KEY CHECK STATUS ===\n";
    $result = $conn->query("PRAGMA foreign_keys");
    $fk_status = $result->fetchColumn();
    echo "Foreign keys enabled: " . ($fk_status ? "YES" : "NO") . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>