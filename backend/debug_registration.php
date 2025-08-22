<?php
// Create as backend/debug_registration.php
require_once "config.php";
header('Content-Type: text/plain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== REGISTRATION DEBUG REPORT ===\n\n";

// Check database connection
try {
    echo "Database connection: ";
    if ($conn) {
        echo "✓ CONNECTED\n";
        echo "Database type: " . $conn->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    } else {
        echo "✗ FAILED\n";
        exit;
    }
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    exit;
}

// Check users table structure
echo "\n=== USERS TABLE CHECK ===\n";
try {
    $result = $conn->query("PRAGMA table_info(users)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($columns)) {
        echo "✗ Users table does not exist!\n";
        echo "Run reset_db.php to create the database structure.\n\n";
        
        // Try to create the table
        echo "Attempting to create users table...\n";
        $conn->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "✓ Users table created successfully!\n\n";
    } else {
        echo "✓ Users table exists with columns:\n";
        foreach ($columns as $column) {
            echo "  - {$column['name']}: {$column['type']}" . 
                 ($column['notnull'] ? " NOT NULL" : "") .
                 ($column['dflt_value'] ? " DEFAULT {$column['dflt_value']}" : "") . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Table check error: " . $e->getMessage() . "\n";
}

// Test basic database operations
echo "\n=== DATABASE OPERATIONS TEST ===\n";
try {
    // Test SELECT
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "✓ SELECT test passed (found $count users)\n";
    
    // Test INSERT (with rollback)
    $conn->beginTransaction();
    $testStmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $testResult = $testStmt->execute(['test_debug_user', 'test_password', 'user']);
    $testId = $conn->lastInsertId();
    $conn->rollback(); // Don't actually save the test user
    
    if ($testResult && $testId) {
        echo "✓ INSERT test passed (would create ID: $testId)\n";
    } else {
        echo "✗ INSERT test failed\n";
    }
    
} catch (Exception $e) {
    echo "✗ Database operation error: " . $e->getMessage() . "\n";
}

// Check file permissions
echo "\n=== FILE PERMISSIONS CHECK ===\n";
$db_file = __DIR__ . "/voting.db";
if (file_exists($db_file)) {
    $perms = fileperms($db_file);
    echo "Database file: ✓ EXISTS\n";
    echo "Permissions: " . decoct($perms & 0777) . "\n";
    echo "Writable: " . (is_writable($db_file) ? "✓ YES" : "✗ NO") . "\n";
} else {
    echo "Database file: ✗ NOT FOUND\n";
}

$dir_writable = is_writable(__DIR__);
echo "Directory writable: " . ($dir_writable ? "✓ YES" : "✗ NO") . "\n";

// Test JSON parsing
echo "\n=== JSON PARSING TEST ===\n";
$test_json = '{"username":"testuser","password":"testpass123"}';
$parsed = json_decode($test_json, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "✓ JSON parsing works correctly\n";
    echo "Test data: username={$parsed['username']}, password length=" . strlen($parsed['password']) . "\n";
} else {
    echo "✗ JSON parsing error: " . json_last_error_msg() . "\n";
}

// Test password hashing
echo "\n=== PASSWORD HASHING TEST ===\n";
$test_password = "testpass123";
$hashed = password_hash($test_password, PASSWORD_BCRYPT);
if ($hashed && password_verify($test_password, $hashed)) {
    echo "✓ Password hashing works correctly\n";
} else {
    echo "✗ Password hashing failed\n";
}

// Check session functionality
echo "\n=== SESSION TEST ===\n";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['test'] = 'working';
if (isset($_SESSION['test']) && $_SESSION['test'] === 'working') {
    echo "✓ Sessions work correctly\n";
} else {
    echo "✗ Session functionality failed\n";
}

// Test actual registration endpoint
echo "\n=== REGISTRATION ENDPOINT TEST ===\n";
try {
    // Simulate a registration request
    $test_data = json_encode(['username' => 'debug_test_' . time(), 'password' => 'testpass123']);
    
    // This would normally be done via HTTP, but let's test the logic directly
    echo "Test registration data prepared\n";
    echo "Next step: Test the registration endpoint via browser or curl\n";
    
} catch (Exception $e) {
    echo "✗ Endpoint test error: " . $e->getMessage() . "\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. If users table doesn't exist, run: reset_db.php\n";
echo "2. Check file permissions on voting.db and backend directory\n";
echo "3. Enable error reporting in PHP for more detailed errors\n";
echo "4. Test registration with: curl -X POST -H 'Content-Type: application/json' -d '{\"username\":\"testuser\",\"password\":\"testpass123\"}' http://yoursite/backend/register.php\n";

echo "\n=== DEBUG COMPLETE ===\n";
?>