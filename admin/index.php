<?php
require_once '../config.php';

// Authentication: Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all menu items from the database
$result = $conn->query("SELECT * FROM menu_items ORDER BY category, id DESC");
$items = $result->fetch_all(MYSQLI_ASSOC);

// Group items by their category for structured display
$grouped_items = [];
foreach ($items as $item) {
    $grouped_items[$item['category']][] = $item;
}

// Define the order of categories
$categories = ['نوشیدنی گرم', 'نوشیدنی سرد', 'شیک‌ها', 'آیتم‌های ویژه'];

// Handle session messages (e.g., for success or error feedback)
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Re-use some base styles -->
    <style>
        :root {
            --admin-bg: #f4f7f6;
            --sidebar-bg: #4B3621;
            --content-bg: #ffffff;
            --accent-color: #8B5E34;
            --text-light: #ffffff;
            --border-color: #e0e0e0;
        }
        body {
            background-color: var(--admin-bg);
            display: flex;
        }
        .admin-sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            color: var(--text-light);
            height: 100vh;
            position: fixed;
            right: 0;
            padding: 20px;
        }
        .admin-sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .admin-sidebar ul { list-style: none; padding: 0; }
        .admin-sidebar ul li a {
            display: block;
            padding: 15px;
            color: var(--text-light);
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .admin-sidebar ul li a:hover {
            background-color: var(--accent-color);
        }
        .admin-sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 40px);
            text-align: center;
        }

        .admin-main-content {
            margin-right: 250px; /* Same as sidebar width */
            width: calc(100% - 250px);
            padding: 30px;
        }
        .admin-section {
            background-color: var(--content-bg);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .admin-section h2 {
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: var(--font-primary);
        }

        /* Table Styles */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th, .items-table td {
            padding: 12px;
            border: 1px solid var(--border-color);
            text-align: right;
            vertical-align: middle;
        }
        .items-table thead { background-color: #f9f9f9; }
        .items-table img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .actions a {
            margin-left: 10px;
            color: var(--accent-color);
            text-decoration: none;
        }
        .actions a.delete { color: #d9534f; }

        /* Feedback Messages */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fff;
        }
        .message.success { background-color: #28a745; }
        .message.error { background-color: #dc3545; }

        @media (max-width: 992px) {
            .admin-sidebar {
                /* For simplicity on mobile, we can hide the sidebar or make it a top bar */
                /* Hiding is simpler for this scope */
                display: none;
            }
            .admin-main-content {
                margin-right: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <h2>پنل مدیریت</h2>
        <ul>
            <li><a href="#add-item-section">افزودن آیتم</a></li>
            <li><a href="#view-items-section">مشاهده آیتم‌ها</a></li>
        </ul>
        <a href="logout.php" class="logout btn btn-secondary">خروج</a>
    </aside>

    <main class="admin-main-content">
        <h1>داشبورد کافه دنیس</h1>

        <?php if ($message): ?>
            <div class="message <?php echo htmlspecialchars($message['type']); ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php endif; ?>

        <section id="add-item-section" class="admin-section">
            <h2>افزودن آیتم جدید</h2>
            <form action="add_item.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">نام آیتم</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="price">قیمت (به تومان)</label>
                        <input type="number" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="category">دسته بندی</label>
                        <select id="category" name="category" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">تصویر آیتم</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">افزودن</button>
            </form>
        </section>

        <section id="view-items-section" class="admin-section">
            <h2>لیست آیتم‌های منو</h2>
            <?php if (empty($items)): ?>
                <p>هیچ آیتمی در منو وجود ندارد.</p>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <?php if (isset($grouped_items[$category])): ?>
                        <h3><?php echo htmlspecialchars($category); ?></h3>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>تصویر</th>
                                    <th>نام</th>
                                    <th>قیمت</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grouped_items[$category] as $item): ?>
                                    <tr>
                                        <td><img src="../uploads/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"></td>
                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td><?php echo number_format($item['price']); ?> تومان</td>
                                        <td class="actions">
                                            <a href="edit_item.php?id=<?php echo $item['id']; ?>">ویرایش</a>
                                            <a href="delete_item.php?id=<?php echo $item['id']; ?>" class="delete" onclick="return confirm('آیا از حذف این آیتم مطمئن هستید؟');">حذف</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
