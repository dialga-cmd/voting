<?php
// Create this as backend/reset_db.php
$db_file = __DIR__ . "/voting.db";

// Delete old database
if (file_exists($db_file)) {
    unlink($db_file);
    echo "Old database deleted.<br>";
}

try {
    $conn = new PDO("sqlite:$db_file");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Enable foreign key constraints
    $conn->exec("PRAGMA foreign_keys = ON;");

    // Create fresh tables with correct structure
    $conn->exec("
    CREATE TABLE polls (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        start_date TEXT,
        end_date TEXT
    );

    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'user',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE participants (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        poll_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        email TEXT,
        FOREIGN KEY(poll_id) REFERENCES polls(id) ON DELETE CASCADE
    );

    CREATE TABLE votes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        participant_id INTEGER NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(participant_id) REFERENCES participants(id) ON DELETE CASCADE,
        UNIQUE(user_id, participant_id)
    );

    CREATE TABLE student_council (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        position TEXT NOT NULL
    );
    ");

    echo "Fresh database tables created.<br>";

    // Insert default data
    $conn->exec("
        INSERT INTO polls (title, start_date, end_date) 
        VALUES ('Student Union Election 2025', '2025-01-01', '2025-12-31');
    ");

    $conn->exec("
        INSERT INTO participants (name, email, poll_id) VALUES
        ('John Smith', 'john@example.com', 1),
        ('Maria Johnson', 'maria@example.com', 1),
        ('Alex Davis', 'alex@example.com', 1),
        ('Sarah Wilson', 'sarah@example.com', 1);
    ");

    // Create admin user
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')")
         ->execute(['admin', $password]);

    // Create a test user
    $testPassword = password_hash('test123', PASSWORD_BCRYPT);
    $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')")
         ->execute(['testuser', $testPassword]);

    echo "Default data inserted.<br>";
    echo "Admin login: admin / admin123<br>";
    echo "Test user login: testuser / test123<br>";
    echo "<br>Database setup complete!<br>";
    echo "<a href='../index.html'>Go to Voting System</a>";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>