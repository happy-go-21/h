<?php
require_once 'security.php';
secureSession();
session_start();
initializeSessionSecurity();

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    regenerateSessionId();
    session_destroy();
    header('Location: index.html');
    exit;
}

// Secure user storage file outside web root
$users_file = __DIR__ . '/../data/users.txt';

// Create data directory if it doesn't exist
$data_dir = dirname($users_file);
if (!is_dir($data_dir)) {
    if (!mkdir($data_dir, 0755, true)) {
        $_SESSION['error'] = 'System error: Unable to create data directory';
        header('Location: login.php');
        exit;
    }
}

function loadUsers() {
    global $users_file;
    if (!file_exists($users_file)) {
        return [];
    }
    
    $users = [];
    $lines = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $parts = explode('|', trim($line));
        if (count($parts) == 3) {
            $users[trim($parts[0])] = [
                'password' => trim($parts[1]),
                'phone' => trim($parts[2])
            ];
        }
    }
    
    return $users;
}

function saveUser($name, $password, $phone) {
    global $users_file;
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $line = trim($name) . '|' . $hashed_password . '|' . trim($phone) . "\n";
    
    if (file_put_contents($users_file, $line, FILE_APPEND | LOCK_EX) === false) {
        $_SESSION['error'] = 'System error: Unable to save user data';
        return false;
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!validatePOSTCSRFToken()) {
        $_SESSION['error'] = 'Invalid security token. Please try again.';
        header('Location: login.php');
        exit;
    }
    
    $action = $_POST['action'] ?? '';
    $name = sanitizeInput(trim($_POST['name'] ?? ''), 'text');
    $password = $_POST['password'] ?? '';
    $phone = sanitizeInput(trim($_POST['phone'] ?? ''), 'phone');
    
    if ($action == 'register') {
        // Validate registration data
        if (empty($name) || empty($password) || empty($phone)) {
            $_SESSION['error'] = 'All fields are required';
        } else if (strlen($name) < 2 || strlen($name) > 50) {
            $_SESSION['error'] = 'Name must be between 2 and 50 characters';
        } else if (!preg_match('/^[a-zA-Z\s\u0600-\u06FF]+$/u', $name)) {
            $_SESSION['error'] = 'Name can only contain letters and spaces';
        } else if (strlen($password) != 4 || !ctype_digit($password)) {
            $_SESSION['error'] = 'Password must be exactly 4 digits';
        } else if (!preg_match('/^(\+93|0)?7[0-9]{8}$/', $phone)) {
            $_SESSION['error'] = 'Please enter a valid Afghan phone number (07xxxxxxxx)';
        } else {
            $users = loadUsers();
            
            if (isset($users[$name])) {
                $_SESSION['error'] = 'User already exists';
            } else {
                if (saveUser($name, $password, $phone)) {
                    regenerateSessionId();
                    $_SESSION['user'] = $name;
                    $_SESSION['success'] = 'Registration successful! Welcome to Afghanistan Market.';
                    header('Location: index.html');
                    exit;
                } // If saveUser fails, error is already set in session
            }
        }
    } else if ($action == 'login') {
        // Validate login data
        if (empty($name) || empty($password)) {
            $_SESSION['error'] = 'Name and password are required';
        } else if (strlen($password) != 4 || !ctype_digit($password)) {
            $_SESSION['error'] = 'Password must be exactly 4 digits';
        } else {
            $users = loadUsers();
            
            // Check rate limiting BEFORE password verification
            if (!checkRateLimit($name . '_' . $_SERVER['REMOTE_ADDR'])) {
                $_SESSION['error'] = 'Too many login attempts. Please try again later.';
                header('Location: login.php');
                exit;
            }
            
            if (isset($users[$name]) && password_verify($password, $users[$name]['password'])) {
                
                regenerateSessionId();
                $_SESSION['user'] = $name;
                $_SESSION['success'] = 'Login successful! Welcome back.';
                header('Location: index.html');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid name or password';
            }
        }
    }
}

// Redirect back to login page with error
header('Location: login.php');
exit;
?>