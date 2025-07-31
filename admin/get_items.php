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
    $query = "SELECT id, name, category, price, image, created_at FROM menu_items ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $items = $stmt->fetchAll();
    
    // Format the data
    $formatted_items = [];
    foreach ($items as $item) {
        $category_names = [
            'hot_drinks' => 'نوشیدنی گرم',
            'cold_drinks' => 'نوشیدنی سرد',
            'stylish' => 'استایلیش',
            'special' => 'محصولات ویژه'
        ];
        
        $formatted_items[] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'category' => $category_names[$item['category']] ?? $item['category'],
            'category_key' => $item['category'],
            'price' => number_format($item['price']),
            'price_raw' => $item['price'],
            'image' => $item['image'],
            'created_at' => date('Y/m/d H:i', strtotime($item['created_at']))
        ];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $formatted_items,
        'total' => count($formatted_items)
    ]);
    
} catch(PDOException $e) {
    error_log("Get items error: " . $e->getMessage());
    handleError('خطا در بارگذاری اطلاعات');
}
?>