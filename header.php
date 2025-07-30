<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'کافه دنیس'; ?></title>
    <meta name="description" content="کافه دنیس، بهترین میزبان شما برای لحظاتی آرام و دلنشین.">

    <!-- Google Fonts: Vazirmatn -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo rtrim(BASE_URL, '/'); ?>/css/style.css">
</head>
<body>
    <header class="main-header">
        <nav class="navbar container">
            <a href="<?php echo rtrim(BASE_URL, '/'); ?>/index.php" class="navbar-brand">کافه دنیس</a>
            <div class="navbar-links">
                <ul>
                    <li><a href="<?php echo rtrim(BASE_URL, '/'); ?>/index.php">صفحه اصلی</a></li>
                    <li><a href="<?php echo rtrim(BASE_URL, '/'); ?>/about.php">درباره ما</a></li>
                    <li><a href="<?php echo rtrim(BASE_URL, '/'); ?>/menu.php">منو</a></li>
                </ul>
            </div>
            <button class="hamburger" id="hamburger-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </nav>
        <div class="mobile-menu" id="mobile-menu-links">
             <ul>
                <li><a href="<?php echo rtrim(BASE_URL, '/'); ?>/index.php">صفحه اصلی</a></li>
                <li><a href="<?php echo rtrim(BASE_URL, '/'); ?>/about.php">درباره ما</a></li>
                <li><a href="<?php echo rtrim(BASE_URL, '/'); ?>/menu.php">منو</a></li>
            </ul>
        </div>
    </header>
    <main>
