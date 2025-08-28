<?php
/**
 * Authentication System
 * GeoPortfolio Pro - PHP Version
 */

session_start();

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            
            // Update last login
            $this->updateLastLogin($user['id']);
            
            // Log login activity
            $this->logLoginActivity($user['name']);
            
            // Log email notification
            $this->logEmail($user['email'], 'Security Alert: New Login', 
                "Hi {$user['name']},\n\nWe detected a new login to your account at " . date('Y-m-d H:i:s') . ". If this was not you, please contact an administrator immediately.");
            
            return ['success' => true, 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials or inactive account'];
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    
    public function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    public function isAdmin() {
        return $this->hasRole('Admin');
    }
    
    public function isEditor() {
        return $this->hasRole('Editor') || $this->isAdmin();
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /geoportfolio-php/login.php');
            exit;
        }
    }
    
    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header('HTTP/1.0 403 Forbidden');
            die('Access denied. Admin privileges required.');
        }
    }
    
    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW(), updated_at = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }
    
    private function logLoginActivity($userName) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $this->db->prepare("INSERT INTO login_activity (user_name, ip_address, user_agent) VALUES (?, ?, ?)");
        $stmt->execute([$userName, $ipAddress, $userAgent]);
    }
    
    private function logEmail($recipient, $subject, $body) {
        $stmt = $this->db->prepare("INSERT INTO email_log (recipient, subject, body, status) VALUES (?, ?, ?, 'sent')");
        $stmt->execute([$recipient, $subject, $body]);
    }
    
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }
    
    public function createUser($name, $email, $password, $role = 'Editor') {
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
        
        if ($stmt->execute([$name, $email, $hashedPassword, $role])) {
            $userId = $this->db->lastInsertId();
            
            // Log welcome email
            $this->logEmail($email, 'Welcome to GeoPortfolio!', 
                "Hi $name,\n\nAn account has been created for you. You can now log in with your credentials.");
            
            return ['success' => true, 'user_id' => $userId];
        }
        
        return ['success' => false, 'message' => 'Failed to create user'];
    }
}

// Global auth instance
$auth = new Auth();

// Helper functions
function isLoggedIn() {
    global $auth;
    return $auth->isLoggedIn();
}

function getCurrentUser() {
    global $auth;
    return $auth->getCurrentUser();
}

function requireLogin() {
    global $auth;
    $auth->requireLogin();
}

function requireAdmin() {
    global $auth;
    $auth->requireAdmin();
}

function isAdmin() {
    global $auth;
    return $auth->isAdmin();
}

function isEditor() {
    global $auth;
    return $auth->isEditor();
}
?>