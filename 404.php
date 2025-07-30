<?php
// Set a 404 HTTP response code
header("HTTP/1.0 404 Not Found");

$page_title = "صفحه یافت نشد - کافه دنیس";
require_once 'header.php';
?>

<div class="page-content" style="text-align: center; padding: 80px 20px;">
    <div class="container">
        <h1 style="font-size: 6rem; color: var(--accent-brick); margin-bottom: 0; line-height: 1;">۴۰۴</h1>
        <h2 style="font-size: 2.5rem; margin-bottom: 20px; font-weight: 700;">صفحه مورد نظر یافت نشد</h2>
        <p style="font-size: 1.2rem; max-width: 500px; margin: 0 auto 30px;">
            متاسفانه صفحه‌ای که به دنبال آن بودید وجود ندارد. ممکن است آدرس را اشتباه وارد کرده باشید یا صفحه حذف شده باشد.
        </p>
        <a href="<?php echo rtrim(BASE_URL, '/'); ?>/index.php" class="btn btn-primary">بازگشت به صفحه اصلی</a>
    </div>
</div>

<?php require_once 'footer.php'; ?>
