<?php
/**
 * Security utilities for Afghanistan Market
 * Handles CSRF protection, session security, and input validation
 */

/**
 * Start a secure session with proper settings
 */
function secureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Only set session parameters if headers haven't been sent
        if (!headers_sent()) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            // Use secure cookies over HTTPS
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
        }
        
        session_start();
    }
}

/**
 * Initialize session security measures
 */
function initializeSessionSecurity() {
    // Regenerate session ID periodically (only if headers not sent)
    if (!isset($_SESSION['initiated']) && !headers_sent()) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    } else if (!isset($_SESSION['initiated'])) {
        $_SESSION['initiated'] = true;
    }
    
    // Check session timeout (30 minutes)
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
    
    // Validate user agent
    if (isset($_SESSION['user_agent'])) {
        if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            session_unset();
            session_destroy();
            session_start();
        }
    } else {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * @param string $token Token to validate
 * @return bool True if valid, false otherwise
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate CSRF token from POST data
 * @return bool True if valid, false otherwise
 */
function validatePOSTCSRFToken() {
    $token = $_POST['csrf_token'] ?? '';
    return validateCSRFToken($token);
}

/**
 * Get CSRF token input field for forms
 * @return string HTML input field with CSRF token
 */
function getCSRFTokenField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Clean up expired CSRF tokens from session
 */
function cleanupExpiredTokens() {
    // This function is kept for backward compatibility
    // but not needed with single token approach
}

/**
 * Regenerate session ID on login/logout
 */
function regenerateSessionId() {
    if (!headers_sent()) {
        session_regenerate_id(true);
        $_SESSION['regenerated'] = true;
    }
}

/**
 * Sanitize and validate input
 * @param string $input Input to sanitize
 * @param string $type Type of validation (text, phone, email)
 * @return string Sanitized input
 */
function sanitizeInput($input, $type = 'text') {
    if (is_array($input)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $input);
    }
    
    $input = trim($input);
    
    switch ($type) {
        case 'phone':
            // Remove non-numeric characters except +
            $input = preg_replace('/[^0-9+\-\s]/', '', $input);
            break;
        case 'email':
            $input = filter_var($input, FILTER_SANITIZE_EMAIL);
            break;
        case 'text':
        default:
            // Remove potentially dangerous characters
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            break;
    }
    
    return $input;
}

/**
 * Escape output for safe HTML display
 * @param string $output Output to escape
 * @return string Safely escaped output
 */
function escapeOutput($output) {
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate input data
 * @param mixed $data Input data
 * @param string $type Type of validation
 * @param array $options Additional validation options
 * @return bool True if valid, false otherwise
 */
function validateInput($data, $type, $options = []) {
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
            
        case 'phone':
            $pattern = '/^[0-9+\-\s]{7,15}$/';
            return preg_match($pattern, $data);
            
        case 'number':
            $min = $options['min'] ?? null;
            $max = $options['max'] ?? null;
            $flags = FILTER_FLAG_ALLOW_FRACTION;
            
            $number = filter_var($data, FILTER_VALIDATE_FLOAT, $flags);
            if ($number === false) return false;
            
            if ($min !== null && $number < $min) return false;
            if ($max !== null && $number > $max) return false;
            
            return true;
            
        case 'url':
            return filter_var($data, FILTER_VALIDATE_URL) !== false;
            
        case 'required':
            return !empty($data);
            
        case 'length':
            $min = $options['min'] ?? 0;
            $max = $options['max'] ?? PHP_INT_MAX;
            $length = mb_strlen($data, 'UTF-8');
            return $length >= $min && $length <= $max;
            
        default:
            return true;
    }
}

/**
 * Basic rate limiting for login attempts
 * @param string $identifier IP address or username
 * @param int $maxAttempts Maximum attempts allowed
 * @param int $timeWindow Time window in seconds
 * @return bool True if allowed, false if rate limited
 */
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300) {
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['attempts' => 0, 'last_attempt' => time()];
    }
    
    $rateData = $_SESSION[$key];
    $currentTime = time();
    
    // Reset if time window has passed
    if ($currentTime - $rateData['last_attempt'] > $timeWindow) {
        $_SESSION[$key] = ['attempts' => 1, 'last_attempt' => $currentTime];
        return true;
    }
    
    // Check if max attempts exceeded
    if ($rateData['attempts'] >= $maxAttempts) {
        return false;
    }
    
    // Increment attempt count
    $_SESSION[$key]['attempts']++;
    $_SESSION[$key]['last_attempt'] = $currentTime;
    
    return true;
}

/**
 * Log security events
 * @param string $event Event description
 * @param array $context Additional context
 */
function logSecurityEvent($event, $context = []) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $timestamp = date('Y-m-d H:i:s');
    
    $log_entry = [
        'timestamp' => $timestamp,
        'ip' => $ip,
        'user_agent' => $user_agent,
        'event' => $event,
        'context' => $context
    ];
    
    // Log to PHP error log
    error_log("SECURITY: $event - IP: $ip");
    
    // Optionally log to file
    $log_file = __DIR__ . '/../data/security.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_line = json_encode($log_entry) . "\n";
    file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);
}

/**
 * Block suspicious requests
 * @param array $patterns Array of patterns to check
 */
function blockSuspiciousRequests($patterns = []) {
    $default_patterns = [
        'script',
        'javascript:',
        '<script',
        'onload=',
        'onclick=',
        'onerror=',
        '../',
        '..\\',
        'union select',
        'drop table',
        'insert into',
        'delete from'
    ];
    
    $patterns = array_merge($default_patterns, $patterns);
    $check_data = array_merge($_GET, $_POST, $_COOKIE);
    
    foreach ($check_data as $key => $value) {
        if (is_string($value)) {
            $value_lower = strtolower($value);
            foreach ($patterns as $pattern) {
                if (strpos($value_lower, strtolower($pattern)) !== false) {
                    logSecurityEvent('Suspicious request blocked', [
                        'pattern' => $pattern,
                        'key' => $key,
                        'value' => $value
                    ]);
                    
                    http_response_code(403);
                    die('Access denied');
                }
            }
        }
    }
}

/**
 * Clean old session data and rate limits
 */
function cleanOldSessionData() {
    $now = time();
    
    // Clean old rate limits
    if (isset($_SESSION['rate_limits'])) {
        foreach ($_SESSION['rate_limits'] as $key => $data) {
            if ($now - $data['window_start'] > 3600) { // 1 hour
                unset($_SESSION['rate_limits'][$key]);
            }
        }
    }
}

/**
 * Initialize security for the application
 */
function initSecurity() {
    cleanOldSessionData();
}

// Auto-run security checks (only if not an API endpoint)
if (!isset($_GET['api']) && strpos($_SERVER['REQUEST_URI'], 'get_items.php') === false) {
    secureSession();
    initSecurity();
    blockSuspiciousRequests();
}
?>