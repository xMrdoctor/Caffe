<?php
require_once '../config/database.php';
initSecureSession();
setCORSHeaders();

// Check authentication
if (!checkAdminAuth()) {
    handleError('غیر مجاز', 401);
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('روش درخواست نامعتبر', 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    handleError('داده‌های ورودی نامعتبر');
}

// Validate input
$item_id = intval($input['id'] ?? 0);

if ($item_id <= 0) {
    handleError('شناسه محصول نامعتبر است');
}

// Rate limiting
if (!checkRateLimit('delete_item', 20, 60)) {
    handleError('تعداد درخواست‌ها زیاد است', 429);
}

// Initialize database
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    handleError('خطا در اتصال به پایگاه داده');
}

try {
    // First, get the item to check if image needs to be deleted
    $query = "SELECT name, image FROM menu_items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();
    
    if (!$item) {
        handleError('محصول یافت نشد');
    }
    
    // Delete the item from database
    $query = "DELETE FROM menu_items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$item_id]);
    
    if ($stmt->rowCount() === 0) {
        handleError('محصول یافت نشد');
    }
    
    // Delete associated image file if exists
    if ($item['image'] && file_exists(UPLOAD_PATH . $item['image'])) {
        unlink(UPLOAD_PATH . $item['image']);
    }
    
    // Log the action
    logSecurityEvent('ITEM_DELETED', "Item ID: $item_id, Name: " . $item['name']);
    
    echo json_encode([
        'success' => true,
        'message' => 'محصول با موفقیت حذف شد'
    ]);
    
} catch(PDOException $e) {
    error_log("Delete item error: " . $e->getMessage());
    handleError('خطا در حذف محصول');
}
?>