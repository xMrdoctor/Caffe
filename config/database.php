<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'cafe_dennis');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Security Configuration
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', password_hash('CafeDennis2024!', PASSWORD_DEFAULT));
define('SESSION_TIMEOUT', 3600); // 1 hour

// Upload Configuration
define('UPLOAD_PATH', '../images/menu/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Site Configuration
define('SITE_NAME', 'کافه دنیس');
define('SITE_URL', 'http://localhost/cafe-dennis/');

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            return false;
        }
        
        return $this->conn;
    }
    
    public function createTables() {
        try {
            $sql = "
            CREATE TABLE IF NOT EXISTS menu_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                category VARCHAR(100) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                image VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_category (category),
                INDEX idx_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS admin_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(128) NOT NULL UNIQUE,
                user_id VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                expires_at TIMESTAMP NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                INDEX idx_session (session_id),
                INDEX idx_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            $this->conn->exec($sql);
            return true;
        } catch(PDOException $exception) {
            error_log("Table creation error: " . $exception->getMessage());
            return false;
        }
    }
}

// Security Functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

function generateCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function checkAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

function logSecurityEvent($event, $details = '') {
    $log_entry = date('Y-m-d H:i:s') . " - " . $event . " - " . $details . " - IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    error_log($log_entry, 3, '../logs/security.log');
}

// Rate limiting
function checkRateLimit($action, $limit = 5, $window = 300) {
    $key = $action . '_' . $_SERVER['REMOTE_ADDR'];
    $file = '../cache/' . md5($key) . '.tmp';
    
    $attempts = [];
    if (file_exists($file)) {
        $attempts = json_decode(file_get_contents($file), true) ?: [];
    }
    
    $now = time();
    $attempts = array_filter($attempts, function($time) use ($now, $window) {
        return ($now - $time) < $window;
    });
    
    if (count($attempts) >= $limit) {
        return false;
    }
    
    $attempts[] = $now;
    file_put_contents($file, json_encode($attempts));
    
    return true;
}

// Image upload security
function validateImage($file) {
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'فایل بیش از حد مجاز بزرگ است'];
    }
    
    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'فرمت فایل مجاز نیست'];
    }
    
    // Check MIME type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'نوع فایل مجاز نیست'];
    }
    
    // Check if it's actually an image
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return ['success' => false, 'message' => 'فایل تصویر معتبر نیست'];
    }
    
    return ['success' => true];
}

function uploadImage($file) {
    $validation = validateImage($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('menu_', true) . '.' . $extension;
    $target_path = UPLOAD_PATH . $filename;
    
    // Create directory if it doesn't exist
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'خطا در آپلود فایل'];
    }
}

// Error handling
function handleError($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'code' => $code
    ]);
    exit;
}

// CORS headers for API
function setCORSHeaders() {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
}

// Initialize session securely
function initSecureSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    
    session_start();
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Create necessary directories
function createDirectories() {
    $dirs = [
        '../images/menu',
        '../logs',
        '../cache'
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Create .htaccess for security
    $htaccess_content = "
Options -Indexes
<Files *.log>
    Order allow,deny
    Deny from all
</Files>
";
    
    file_put_contents('../logs/.htaccess', $htaccess_content);
    file_put_contents('../cache/.htaccess', $htaccess_content);
}

// Initialize everything
createDirectories();
?>