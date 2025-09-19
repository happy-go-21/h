<?php
require_once 'database.php';

// Simple sanitization function for API
function sanitizeInput($input, $type = 'text') {
    if (is_array($input)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $input);
    }
    
    $input = trim($input);
    
    switch ($type) {
        case 'phone':
            $input = preg_replace('/[^0-9+\-\s]/', '', $input);
            break;
        case 'email':
            $input = filter_var($input, FILTER_SANITIZE_EMAIL);
            break;
        case 'text':
        default:
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            break;
    }
    
    return $input;
}

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Build filters from GET parameters
    $filters = [];
    
    if (!empty($_GET['category'])) {
        $filters['category'] = sanitizeInput($_GET['category'], 'text');
    }
    
    if (!empty($_GET['city'])) {
        $filters['city'] = sanitizeInput($_GET['city'], 'text');
    }
    
    if (!empty($_GET['q'])) {
        $filters['search'] = sanitizeInput($_GET['q'], 'text');
    }
    
    if (!empty($_GET['search'])) {
        $filters['search'] = sanitizeInput($_GET['search'], 'text');
    }
    
    // Pagination parameters
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(50, intval($_GET['limit']))) : 12;
    $offset = ($page - 1) * $limit;
    
    // Get items from database
    $items = getItems($filters, $limit, $offset);
    $totalCount = getItemsCount($filters);
    $totalPages = ceil($totalCount / $limit);
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'items' => $items,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalCount,
            'items_per_page' => $limit,
            'offset' => $offset
        ],
        'filters' => $filters
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('API Error in get_items.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'خطا در بارگذاری آگهی‌ها',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>