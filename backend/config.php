<?php
$db_file = __DIR__ . "/voting.db";

try {
    $conn = new PDO("sqlite:$db_file");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Enforce foreign key constraints
    $conn->exec("PRAGMA foreign_keys = ON;");

    // Create tables with consistent naming
    $conn->exec("
    CREATE TABLE IF NOT EXISTS polls (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        start_date TEXT,
        end_date TEXT
    );

    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'user',
        poll_id INTEGER,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS participants (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        poll_id INTEGER,
        name TEXT NOT NULL,
        email TEXT,
        FOREIGN KEY(poll_id) REFERENCES polls(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS votes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        participant_id INTEGER,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(participant_id) REFERENCES participants(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS student_council (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        position TEXT
    );
    ");

    // Check if we need to migrate the votes table
    $result = $conn->query("PRAGMA table_info(votes)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    $hasParticipantsId = false;
    $hasParticipantId = false;
    
    foreach ($columns as $column) {
        if ($column['name'] === 'participants_id') {
            $hasParticipantsId = true;
        }
        if ($column['name'] === 'participant_id') {
            $hasParticipantId = true;
        }
    }

    // If we have the old column name, migrate it
    if ($hasParticipantsId && !$hasParticipantId) {
        $conn->exec("
            CREATE TABLE votes_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                participant_id INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(participant_id) REFERENCES participants(id) ON DELETE CASCADE
            );
            
            INSERT INTO votes_new (id, user_id, participant_id, created_at)
            SELECT id, user_id, participants_id, created_at FROM votes;
            
            DROP TABLE votes;
            ALTER TABLE votes_new RENAME TO votes;
        ");
    }

    // Insert default poll if none exists
    $stmt = $conn->query("SELECT COUNT(*) FROM polls");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("
            INSERT INTO polls (title, start_date, end_date) 
            VALUES ('Student Union Election 2025', '2025-10-15', '2025-10-20');
        ");
    }

    // Insert default participants if empty
    $stmt = $conn->query("SELECT COUNT(*) FROM participants");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("
            INSERT INTO participants (name, email, poll_id) VALUES
            ('John Smith', 'john@example.com', 1),
            ('Maria Johnson', 'maria@example.com', 1),
            ('Alex Davis', 'alex@example.com', 1);
        ");
    }

    // Insert admin if none exists
    $checkAdmin = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($checkAdmin == 0) {
        $password = password_hash('admin123', PASSWORD_BCRYPT);
        $conn->prepare("INSERT INTO users (username, password, role, poll_id) VALUES (?, ?, 'admin', NULL)")
             ->execute(['admin', $password]);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}
?>