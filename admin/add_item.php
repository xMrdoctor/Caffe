<?php
require_once '../config.php';

// --- Security: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    // Set a generic error message and redirect to login
    $_SESSION['message'] = ['type' => 'error', 'text' => 'لطفا برای دسترسی به این صفحه وارد شوید.'];
    header('Location: login.php');
    exit;
}

// --- Handle POST request from the form ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- 1. Sanitize and Validate Inputs ---
    $title = trim($_POST['title'] ?? '');
    $price = filter_var($_POST['price'], FILTER_VALIDATE_INT);
    $category = trim($_POST['category'] ?? '');
    $image = $_FILES['image'] ?? null;

    $allowed_categories = ['نوشیدنی گرم', 'نوشیدنی سرد', 'شیک‌ها', 'آیتم‌های ویژه'];

    if (empty($title) || $price === false || empty($category) || !in_array($category, $allowed_categories) || $image['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'لطفا تمام فیلدها را به درستی پر کنید.'];
        header('Location: index.php#add-item-section');
        exit;
    }

    // --- 2. Secure File Upload Handling ---
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Check for upload errors
    if ($image['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'خطا در هنگام آپلود فایل. کد خطا: ' . $image['error']];
        header('Location: index.php#add-item-section');
        exit;
    }

    // Validate file type and size
    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_mime_type = mime_content_type($image['tmp_name']);
    if (!in_array($file_mime_type, $allowed_mime_types)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'فرمت فایل تصویر مجاز نیست. فقط (JPEG, PNG, GIF, WEBP).'];
        header('Location: index.php#add-item-section');
        exit;
    }

    if ($image['size'] > 2 * 1024 * 1024) { // 2MB limit
        $_SESSION['message'] = ['type' => 'error', 'text' => 'اندازه فایل نباید بیشتر از 2 مگابایت باشد.'];
        header('Location: index.php#add-item-section');
        exit;
    }

    // Generate a unique filename to prevent overwriting and add security
    $image_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $unique_filename = bin2hex(random_bytes(16)) . '.' . $image_extension;
    $target_file = $target_dir . $unique_filename;

    // Move the uploaded file
    if (move_uploaded_file($image['tmp_name'], $target_file)) {
        // --- 3. Database Insertion ---
        $stmt = $conn->prepare("INSERT INTO menu_items (title, price, category, image_url) VALUES (?, ?, ?, ?)");
        // s = string, i = integer
        $stmt->bind_param("siss", $title, $price, $category, $unique_filename);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'آیتم با موفقیت اضافه شد.'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'خطا در افزودن آیتم به دیتابیس: ' . $stmt->error];
            // Clean up: delete the uploaded file if DB insert fails
            unlink($target_file);
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'خطا در ذخیره سازی فایل آپلود شده.'];
    }

    $conn->close();
    header('Location: index.php#view-items-section');
    exit;
} else {
    // If accessed directly, redirect away
    header('Location: index.php');
    exit;
}
?>
