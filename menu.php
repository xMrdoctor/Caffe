<?php
require_once 'config/database.php';
initSecureSession();

// Initialize database and create tables
$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    $database->createTables();
}

// Fetch menu items
$menu_items = [];
try {
    $query = "SELECT * FROM menu_items ORDER BY category, name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $menu_items = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Menu fetch error: " . $e->getMessage());
}

// Get categories for filtering
$categories = [];
foreach ($menu_items as $item) {
    if (!in_array($item['category'], $categories)) {
        $categories[] = $item['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کافه دنیس - منو</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="description" content="منوی کامل کافه دنیس - نوشیدنی‌های گرم و سرد، محصولات ویژه">
    <meta name="keywords" content="کافه دنیس, منو, قهوه, نوشیدنی, کافه">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <i class="fas fa-coffee"></i>
                    <span>کافه دنیس</span>
                </div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="index.html" class="nav-link">خانه</a>
                    </li>
                    <li class="nav-item">
                        <a href="menu.php" class="nav-link active">منو</a>
                    </li>
                    <li class="nav-item">
                        <a href="about.html" class="nav-link">درباره ما</a>
                    </li>
                </ul>
                <div class="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Menu Hero Section -->
    <section class="menu-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">منوی کافه دنیس</h1>
            <p class="hero-subtitle">طعم‌های بی‌نظیر در انتظار شماست</p>
            <div class="qr-code-info">
                <i class="fas fa-qrcode"></i>
                <p>برای مشاهده منو کامل QR کد را اسکن کنید</p>
            </div>
        </div>
    </section>

    <!-- Menu Filters -->
    <section class="menu-filters">
        <div class="container">
            <div class="filter-buttons">
                <button class="filter-btn active" data-category="all">همه</button>
                <?php if (in_array('cold_drinks', $categories)): ?>
                <button class="filter-btn" data-category="cold_drinks">نوشیدنی‌های سرد</button>
                <?php endif; ?>
                <?php if (in_array('hot_drinks', $categories)): ?>
                <button class="filter-btn" data-category="hot_drinks">نوشیدنی‌های گرم</button>
                <?php endif; ?>
                <?php if (in_array('stylish', $categories)): ?>
                <button class="filter-btn" data-category="stylish">استایلیش</button>
                <?php endif; ?>
                <?php if (in_array('special', $categories)): ?>
                <button class="filter-btn" data-category="special">محصولات ویژه</button>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Menu Content -->
    <section class="menu-content">
        <div class="container">
            <div class="menu-grid">
                <?php if (empty($menu_items)): ?>
                    <!-- Sample items when database is empty -->
                    <div class="menu-item" data-category="hot_drinks">
                        <div class="menu-item-image">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <div class="menu-item-content">
                            <h3>اسپرسو</h3>
                            <div class="menu-item-price">25000</div>
                        </div>
                    </div>
                    
                    <div class="menu-item" data-category="hot_drinks">
                        <div class="menu-item-image">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <div class="menu-item-content">
                            <h3>کاپوچینو</h3>
                            <div class="menu-item-price">35000</div>
                        </div>
                    </div>
                    
                    <div class="menu-item" data-category="hot_drinks">
                        <div class="menu-item-image">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <div class="menu-item-content">
                            <h3>لاته</h3>
                            <div class="menu-item-price">40000</div>
                        </div>
                    </div>
                    
                    <div class="menu-item" data-category="cold_drinks">
                        <div class="menu-item-image">
                            <i class="fas fa-glass-whiskey"></i>
                        </div>
                        <div class="menu-item-content">
                            <h3>آیس کافه</h3>
                            <div class="menu-item-price">38000</div>
                        </div>
                    </div>
                    
                    <div class="menu-item" data-category="cold_drinks">
                        <div class="menu-item-image">
                            <i class="fas fa-cocktail"></i>
                        </div>
                        <div class="menu-item-content">
                            <h3>فراپه</h3>
                            <div class="menu-item-price">42000</div>
                        </div>
                    </div>
                    
                    <div class="menu-item" data-category="stylish">
                        <div class="menu-item-image">
                            <i class="fas fa-wine-glass"></i>
                        </div>
                        <div class="menu-item-content">
                            <h3>موکا استایلیش</h3>
                            <div class="menu-item-price">45000</div>
                        </div>
                    </div>
                    
                    <div class="menu-item" data-category="special">
                        <div class="menu-item-image">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="menu-item-content">
                            <h3>قهوه ویژه دنیس</h3>
                            <div class="menu-item-price">55000</div>
                        </div>
                    </div>
                    
                    <div class="menu-item" data-category="special">
                        <div class="menu-item-image">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="menu-item-content">
                            <h3>ترکیب طلایی</h3>
                            <div class="menu-item-price">60000</div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($menu_items as $item): ?>
                    <div class="menu-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                        <div class="menu-item-image">
                            <?php if ($item['image']): ?>
                                <img src="images/menu/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <i class="fas fa-coffee"></i>
                            <?php endif; ?>
                        </div>
                        <div class="menu-item-content">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <div class="menu-item-price"><?php echo number_format($item['price']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- QR Code Section -->
    <section class="qr-section">
        <div class="container">
            <div class="qr-content">
                <div class="qr-text">
                    <h2>دسترسی سریع به منو</h2>
                    <p>برای مشاهده منو روی گوشی خود، QR کد زیر را اسکن کنید</p>
                </div>
                <div class="qr-code">
                    <div class="qr-placeholder">
                        <i class="fas fa-qrcode"></i>
                        <p>QR Code</p>
                        <small>اسکن کنید</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-coffee"></i>
                        <span>کافه دنیس</span>
                    </div>
                    <p>جایی که طعم اصیل قهوه با فضایی گرم و دوستانه ترکیب می‌شود</p>
                </div>
                <div class="footer-section">
                    <h4>دسترسی سریع</h4>
                    <ul>
                        <li><a href="index.html">خانه</a></li>
                        <li><a href="menu.php">منو</a></li>
                        <li><a href="about.html">درباره ما</a></li>
                        <li><a href="admin/login.php">مدیریت</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>تماس با ما</h4>
                    <div class="contact-info">
                        <p><i class="fas fa-map-marker-alt"></i> فلکه مفید کوچه ۵۶ صدوقی ساختمان اول سمت راست ساختمان پاسارگاد</p>
                        <p><i class="fab fa-instagram"></i> cafe__denis</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; ۱۴۰۳ کافه دنیس. تمامی حقوق محفوظ است.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    
    <style>
    .qr-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, #f5f3f0, #faf8f5);
    }
    
    .qr-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
        align-items: center;
    }
    
    .qr-text h2 {
        color: #8b4513;
        font-size: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    
    .qr-text p {
        color: #666;
        font-size: 1.1rem;
        line-height: 1.6;
    }
    
    .qr-placeholder {
        background: linear-gradient(45deg, #d4a574, #cd853f);
        width: 200px;
        height: 200px;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        box-shadow: 0 15px 35px rgba(139, 69, 19, 0.3);
        margin: 0 auto;
    }
    
    .qr-placeholder i {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    
    .qr-placeholder p {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .qr-placeholder small {
        opacity: 0.8;
    }
    
    .qr-code-info {
        margin-top: 2rem;
        text-align: center;
        color: rgba(255, 255, 255, 0.9);
    }
    
    .qr-code-info i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    @media (max-width: 768px) {
        .qr-content {
            grid-template-columns: 1fr;
            text-align: center;
        }
        
        .qr-placeholder {
            width: 150px;
            height: 150px;
        }
        
        .qr-placeholder i {
            font-size: 3rem;
        }
    }
    </style>
</body>
</html>
