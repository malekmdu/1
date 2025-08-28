<?php
/**
 * Common Functions
 * GeoPortfolio Pro - PHP Version
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect with message
 */
function redirect($url, $message = '', $type = 'info') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit;
}

/**
 * Get flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Truncate text
 */
function truncateText($text, $length = 150, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get site setting
 */
function getSiteSetting($key, $default = '') {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

/**
 * Update site setting
 */
function updateSiteSetting($key, $value) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    return $stmt->execute([$key, $value, $value]);
}

/**
 * Get contact info
 */
function getContactInfo() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM contact_info ORDER BY id DESC LIMIT 1");
    return $stmt->fetch() ?: ['email' => '', 'phone' => '', 'address' => ''];
}

/**
 * Get profile data
 */
function getProfileData() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM profile_data ORDER BY id DESC LIMIT 1");
    return $stmt->fetch();
}

/**
 * Upload file helper
 */
function uploadFile($file, $targetDir = 'assets/images/', $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error'];
    }
    
    $fileName = basename($file['name']);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    $newFileName = uniqid() . '.' . $fileType;
    $targetPath = $targetDir . $newFileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'path' => $targetPath];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Paginate results
 */
function paginate($query, $params = [], $page = 1, $perPage = 10) {
    $db = getDB();
    $offset = ($page - 1) * $perPage;
    
    // Count total records
    $countQuery = preg_replace('/SELECT .+ FROM/', 'SELECT COUNT(*) as total FROM', $query);
    $stmt = $db->prepare($countQuery);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    
    // Get paginated results
    $query .= " LIMIT $offset, $perPage";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    return [
        'data' => $results,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage),
            'has_next' => $page < ceil($total / $perPage),
            'has_prev' => $page > 1
        ]
    ];
}

/**
 * Generate breadcrumbs
 */
function generateBreadcrumbs($items) {
    $breadcrumbs = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    $count = count($items);
    
    foreach ($items as $index => $item) {
        if ($index === $count - 1) {
            $breadcrumbs .= '<li class="breadcrumb-item active" aria-current="page">' . $item['title'] . '</li>';
        } else {
            $breadcrumbs .= '<li class="breadcrumb-item"><a href="' . $item['url'] . '">' . $item['title'] . '</a></li>';
        }
    }
    
    $breadcrumbs .= '</ol></nav>';
    return $breadcrumbs;
}
?>