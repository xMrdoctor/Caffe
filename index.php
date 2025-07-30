<?php
$page_title = "کافه دنیس - صفحه اصلی";
require_once 'header.php';
?>

<section class="hero">
    <div class="hero-content" style="animation: fadeIn 1.5s ease-in-out;">
        <h1>به کافه دنیس خوش آمدید</h1>
        <p>جایی برای لحظات گرم و خاطره‌انگیز شما</p>
        <div class="hero-cta">
            <a href="<?php echo rtrim(BASE_URL, '/'); ?>/menu.php" class="btn btn-primary">اسکن منو</a>
            <a href="<?php echo rtrim(BASE_URL, '/'); ?>/about.php" class="btn btn-secondary">درباره ما</a>
        </div>
    </div>
</section>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php require_once 'footer.php'; ?>
