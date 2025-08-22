<?php
// backend/register.php - FIXED VERSION
require_once "config.php";
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();

try {
    // Get input data
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Check if JSON decoding failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["success" => false, "message" => "Invalid JSON data"]);
        exit;
    }
    
    $username = trim($input["username"] ?? "");
    $password = trim($input["password"] ?? "");

    // Validation
    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Username and password required"]);
        exit;
    }

    if (strlen($username) < 3) {
        echo json_encode(["success" => false, "message" => "Username must be at least 3 characters long"]);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(["success" => false, "message" => "Password must be at least 6 characters long"]);
        exit;
    }

    // Check database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . implode(", ", $conn->errorInfo()));
    }
    
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Username already exists"]);
        exit;
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    if (!$hashed) {
        throw new Exception("Failed to hash password");
    }

    // Insert new user with explicit column names
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, created_at) VALUES (?, ?, 'user', datetime('now'))");
    if (!$stmt) {
        throw new Exception("Failed to prepare insert statement: " . implode(", ", $conn->errorInfo()));
    }

    $success = $stmt->execute([$username, $hashed]);
    if (!$success) {
        throw new Exception("Failed to insert user: " . implode(", ", $stmt->errorInfo()));
    }

    // Get the newly created user ID
    $userId = $conn->lastInsertId();
    if (!$userId) {
        throw new Exception("Failed to get new user ID");
    }

    // Set up session for auto-login
    $_SESSION["user"] = [
        "id" => $userId,
        "username" => $username,
        "role" => "user"
    ];

    // Return success response
    echo json_encode([
        "success" => true, 
        "message" => "Registration successful",
        "username" => $username,
        "id" => (int)$userId,
        "role" => "user"
    ]);

} catch (PDOException $e) {
    error_log("PDO Registration error: " . $e->getMessage());
    echo json_encode([
        "success" => false, 
        "message" => "Database error: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode([
        "success" => false, 
        "message" => "Registration failed: " . $e->getMessage()
    ]);
}
?>