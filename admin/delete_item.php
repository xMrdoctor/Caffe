<?php
require_once '../config.php';

// --- Security: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'لطفا برای دسترسی به این صفحه وارد شوید.'];
    header('Location: login.php');
    exit;
}

// --- Get Item ID from GET request ---
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'شناسه آیتم نامعتبر است.'];
    header('Location: index.php');
    exit;
}

// --- 1. Fetch the image filename before deleting the DB record ---
$stmt = $conn->prepare("SELECT image_url FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $item = $result->fetch_assoc();
    $image_filename = $item['image_url'];
    $image_path = '../uploads/' . $image_filename;

    // --- 2. Delete the record from the database ---
    $delete_stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        // --- 3. Delete the associated image file from the server ---
        if (!empty($image_filename) && file_exists($image_path)) {
            // Suppress errors in case file is already gone, but log if you need to.
            @unlink($image_path);
        }
        $_SESSION['message'] = ['type' => 'success', 'text' => 'آیتم با موفقیت حذف شد.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'خطا در هنگام حذف آیتم از دیتابیس.'];
    }
    $delete_stmt->close();
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'آیتم مورد نظر برای حذف یافت نشد.'];
}

$stmt->close();
$conn->close();

// --- Redirect back to the admin dashboard ---
header('Location: index.php#view-items-section');
exit;
?>
