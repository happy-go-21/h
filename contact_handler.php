<?php
require_once 'security.php';
secureSession();
session_start();
initializeSessionSecurity();
require_once 'database.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Validate CSRF token for AJAX requests
if (!validatePOSTCSRFToken()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

// Get POST data
$action = sanitizeInput($_POST['action'] ?? '', 'text');
$item_id = intval($_POST['item_id'] ?? 0);
$contact_type = sanitizeInput($_POST['contact_type'] ?? 'view_contact', 'text');

if ($action === 'log_contact' && $item_id > 0) {
    // Get buyer IP for tracking
    $buyer_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Get buyer info if user is logged in
    $buyer_name = $_SESSION['user'] ?? '';
    $buyer_phone = '';
    
    // Get buyer phone from users file if logged in
    if ($buyer_name) {
        $users_file = __DIR__ . '/../data/users.txt';
        if (file_exists($users_file)) {
            $lines = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $parts = explode('|', trim($line));
                if (count($parts) == 3 && trim($parts[0]) == $buyer_name) {
                    $buyer_phone = trim($parts[2]);
                    break;
                }
            }
        }
    }
    
    // Validate inputs
    if ($item_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid item ID']);
        exit;
    }
    
    // Validate contact type
    $valid_types = ['view_contact', 'whatsapp', 'call', 'copy_phone'];
    if (!in_array($contact_type, $valid_types)) {
        $contact_type = 'view_contact';
    }
    
    if ($action !== 'log_contact') {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
    }
    
    // Log the contact
    $success = logContact($item_id, $contact_type, $buyer_ip, $buyer_name, $buyer_phone);
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Contact logged successfully',
            'contact_type' => $contact_type,
            'item_id' => $item_id
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Failed to log contact'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid request parameters'
    ]);
}
?>