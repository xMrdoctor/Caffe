<?php
$page_title = "منو - کافه دنیس";
require_once 'header.php'; // Includes config.php
require_once 'qr_generator.php'; // Include the QR Code generator

// --- Fetch menu items from the database ---
$result = $conn->query("SELECT * FROM menu_items ORDER BY category, id DESC");
$items = $result->fetch_all(MYSQLI_ASSOC);

// --- Group items by category ---
$grouped_items = [];
foreach ($items as $item) {
    $grouped_items[$item['category']][] = $item;
}

// Define the display order for categories
$categories_order = ['نوشیدنی گرم', 'نوشیدنی سرد', 'شیک‌ها', 'آیتم‌های ویژه'];

// --- Generate QR Code ---
// Get the full URL of the current page
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// We need to capture the output of the QR code generator
ob_start();
QRcode::png($current_url, false, 'L', 5, 2);
$qr_image_data = ob_get_contents();
ob_end_clean();
$qr_image_base64 = 'data:image/png;base64,' . base64_encode($qr_image_data);

$conn->close();
?>

<div class="page-header">
    <div class="container">
        <h1>منوی دیجیتال ما</h1>
    </div>
</div>

<div class="page-content menu-page">
    <div class="container">

        <!-- QR Code Section -->
        <div class="qr-code-section">
            <h2>منو را روی گوشی خود ببینید</h2>
            <p>برای مشاهده آنلاین منو، کد زیر را اسکن کنید.</p>
            <div class="qr-code-img">
                <img src="<?php echo $qr_image_base64; ?>" alt="QR Code for Cafe Denis Menu">
            </div>
        </div>

        <!-- Menu Items Section -->
        <div class="menu-items-container">
            <?php if (empty($items)): ?>
                <p style="text-align: center; font-size: 1.2rem; padding: 50px 0;">
                    منو در حال حاضر خالی است. لطفا به زودی دوباره سر بزنید!
                </p>
            <?php else: ?>
                <?php foreach ($categories_order as $category): ?>
                    <?php if (isset($grouped_items[$category]) && !empty($grouped_items[$category])): ?>

                        <h2 class="menu-category-title"><?php echo htmlspecialchars($category); ?></h2>
                        <div class="menu-items-grid">

                            <?php foreach ($grouped_items[$category] as $item): ?>
                                <div class="menu-item-card">
                                    <div class="card-img">
                                        <img src="<?php echo rtrim(BASE_URL, '/'); ?>/uploads/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    </div>
                                    <div class="card-content">
                                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                        <p class="price"><?php echo number_format($item['price']); ?> تومان</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once 'footer.php'; ?>
