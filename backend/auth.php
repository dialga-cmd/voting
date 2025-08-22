<?php
session_start();
header('Content-Type: application/json');

// Include the config file which should have database connection
require_once __DIR__ . '/config.php';

// Get input and action
$input = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';

try {
    if ($action === 'register') {
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');

        if (!$username || !$password) {
            echo json_encode(['success' => false, 'message' => 'Missing username or password']);
            exit;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
            exit;
        }

        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            exit;
        }

        // Insert new user
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, created_at) VALUES (?, ?, 'user', NOW())");
        $stmt->execute([$username, $hash]);
        
        $userId = $conn->lastInsertId();

        // Auto-login after registration
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user';

        echo json_encode([
            'success' => true, 
            'message' => 'Registration successful',
            'id' => $userId,
            'username' => $username,
            'role' => 'user'
        ]);
        exit;
    }

    if ($action === 'login') {
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');

        if (!$username || !$password) {
            echo json_encode(['success' => false, 'message' => 'Missing username or password']);
            exit;
        }

        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }
        exit;
    }

    if ($action === 'session') {
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => true,
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'] ?? 'user'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No active session']);
        }
        exit;
    }

    if ($action === 'logout') {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
        exit;
    }

    // Invalid action
    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (PDOException $e) {
    error_log("Database error in auth.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Error in auth.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>