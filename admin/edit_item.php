<?php
require_once '../config.php';

// --- Security: Ensure the user is logged in ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'لطفا برای دسترسی به این صفحه وارد شوید.'];
    header('Location: login.php');
    exit;
}

// Get the item ID from the request (works for both GET and POST)
$id = filter_var($_REQUEST['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'شناسه آیتم نامعتبر است.'];
    header('Location: index.php');
    exit;
}

// --- Handle form submission for updating the item ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize and Validate Inputs
    $title = trim($_POST['title'] ?? '');
    $price = filter_var($_POST['price'], FILTER_VALIDATE_INT);
    $category = trim($_POST['category'] ?? '');
    $current_image_url = trim($_POST['current_image_url'] ?? '');
    $new_image = $_FILES['image'] ?? null;

    if (empty($title) || $price === false || empty($category)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'لطفا تمام فیلدها را به درستی پر کنید.'];
        header("Location: edit_item.php?id=$id");
        exit;
    }

    $image_to_update = $current_image_url;

    // 2. Handle optional new image upload
    if ($new_image && $new_image['error'] === UPLOAD_ERR_OK) {
        // (Perform the same validation as in add_item.php)
        $target_dir = "../uploads/";
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array(mime_content_type($new_image['tmp_name']), $allowed_mime_types) || $new_image['size'] > 2 * 1024 * 1024) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'فایل تصویر جدید نامعتبر است.'];
            header("Location: edit_item.php?id=$id");
            exit;
        }

        // Generate a unique filename and move the file
        $image_extension = pathinfo($new_image['name'], PATHINFO_EXTENSION);
        $unique_filename = bin2hex(random_bytes(16)) . '.' . $image_extension;
        $target_file = $target_dir . $unique_filename;

        if (move_uploaded_file($new_image['tmp_name'], $target_file)) {
            // New image uploaded successfully, set it as the one to update
            $image_to_update = $unique_filename;
            // Delete the old image
            if (!empty($current_image_url) && file_exists($target_dir . $current_image_url)) {
                @unlink($target_dir . $current_image_url);
            }
        }
    }

    // 3. Update the database
    $stmt = $conn->prepare("UPDATE menu_items SET title = ?, price = ?, category = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("sissi", $title, $price, $category, $image_to_update, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'آیتم با موفقیت ویرایش شد.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'خطا در ویرایش آیتم.'];
    }
    $stmt->close();
    $conn->close();
    header('Location: index.php#view-items-section');
    exit;
}


// --- Display the form for editing (handles GET request) ---
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'آیتم مورد نظر یافت نشد.'];
    header('Location: index.php');
    exit;
}
$item = $result->fetch_assoc();
$stmt->close();

$categories = ['نوشیدنی گرم', 'نوشیدنی سرد', 'شیک‌ها', 'آیتم‌های ویژه'];

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش آیتم</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Re-using admin styles from index page -->
    <style>
        body { background-color: #f4f7f6; }
        .edit-container { max-width: 800px; margin: 50px auto; }
        .admin-section { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: var(--font-primary); }
        .current-image { max-width: 100px; border-radius: 5px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="edit-container">
        <section class="admin-section">
            <h2>ویرایش آیتم: <?php echo htmlspecialchars($item['title']); ?></h2>
            <form action="edit_item.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                <input type="hidden" name="current_image_url" value="<?php echo htmlspecialchars($item['image_url']); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">نام آیتم</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">قیمت (به تومان)</label>
                        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category">دسته بندی</label>
                        <select id="category" name="category" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($cat === $item['category']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">تصویر جدید (اختیاری)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <p>تصویر فعلی:</p>
                        <img src="../uploads/<?php echo htmlspecialchars($item['image_url']); ?>" alt="Current Image" class="current-image">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                <a href="index.php" class="btn btn-secondary" style="margin-right: 10px;">انصراف</a>
            </form>
        </section>
    </div>
</body>
</html>
