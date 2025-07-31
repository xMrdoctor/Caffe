<?php
require_once '../config/database.php';
initSecureSession();
setCORSHeaders();

// Check authentication
if (!checkAdminAuth()) {
    handleError('غیر مجاز', 401);
}

// Initialize database
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    handleError('خطا در اتصال به پایگاه داده');
}

try {
    // Get total count
    $query = "SELECT COUNT(*) as total FROM menu_items";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $total = $stmt->fetch()['total'];
    
    // Get count by category
    $query = "SELECT category, COUNT(*) as count FROM menu_items GROUP BY category";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    $stats = [
        'total' => $total,
        'hot_drinks' => 0,
        'cold_drinks' => 0,
        'stylish' => 0,
        'special' => 0
    ];
    
    foreach ($categories as $category) {
        $stats[$category['category']] = $category['count'];
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch(PDOException $e) {
    error_log("Get stats error: " . $e->getMessage());
    handleError('خطا در بارگذاری آمار');
}
?>