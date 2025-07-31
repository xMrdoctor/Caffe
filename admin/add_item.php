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

// Validate CSRF token
if (!validateCSRF($_POST['csrf_token'] ?? '')) {
    handleError('درخواست نامعتبر', 403);
}

// Rate limiting
if (!checkRateLimit('add_item', 10, 60)) {
    handleError('تعداد درخواست‌ها زیاد است', 429);
}

// Validate input
$name = sanitizeInput($_POST['name'] ?? '');
$category = sanitizeInput($_POST['category'] ?? '');
$price = floatval($_POST['price'] ?? 0);

if (empty($name) || empty($category) || $price <= 0) {
    handleError('اطلاعات وارد شده نامعتبر است');
}

// Validate category
$allowed_categories = ['hot_drinks', 'cold_drinks', 'stylish', 'special'];
if (!in_array($category, $allowed_categories)) {
    handleError('دسته‌بندی نامعتبر است');
}

// Initialize database
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    handleError('خطا در اتصال به پایگاه داده');
}

$image_filename = null;

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_result = uploadImage($_FILES['image']);
    if (!$upload_result['success']) {
        handleError($upload_result['message']);
    }
    $image_filename = $upload_result['filename'];
}

try {
    // Insert into database
    $query = "INSERT INTO menu_items (name, category, price, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$name, $category, $price, $image_filename]);
    
    $item_id = $conn->lastInsertId();
    
    // Log the action
    logSecurityEvent('ITEM_ADDED', "Item ID: $item_id, Name: $name");
    
    echo json_encode([
        'success' => true,
        'message' => 'محصول با موفقیت اضافه شد',
        'item_id' => $item_id
    ]);
    
} catch(PDOException $e) {
    // Clean up uploaded image if database insert failed
    if ($image_filename && file_exists(UPLOAD_PATH . $image_filename)) {
        unlink(UPLOAD_PATH . $image_filename);
    }
    
    error_log("Add item error: " . $e->getMessage());
    handleError('خطا در ذخیره اطلاعات');
}
?>