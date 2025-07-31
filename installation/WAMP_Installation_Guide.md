# راهنمای نصب و راه‌اندازی کافه دنیس روی WAMP

## مرحله ۱: نصب WAMP Server

### دانلود WAMP
1. به سایت رسمی WAMP مراجعه کنید: [https://www.wampserver.com](https://www.wampserver.com)
2. آخرین نسخه WAMP را دانلود کنید (توصیه می‌شود نسخه 64-bit)
3. فایل دانلود شده را اجرا کنید

### نصب WAMP
1. Setup را با دسترسی Administrator اجرا کنید
2. مسیر نصب پیشنهادی: `C:\wamp64`
3. تمام گزینه‌های پیشنهادی را بپذیرید
4. پس از نصب، WAMP را اجرا کنید
5. منتظر بمانید تا آیکون WAMP در system tray سبز شود

### تنظیمات اولیه WAMP
1. روی آیکون WAMP کلیک راست کنید
2. گزینه "Apache" → "Version" را انتخاب کنید و اطمینان حاصل کنید که نسخه جدیدی نصب است
3. گزینه "PHP" → "Version" را انتخاب کنید و PHP 8.0 یا بالاتر را انتخاب کنید
4. گزینه "MySQL" → "Version" را بررسی کنید

## مرحله ۲: آماده‌سازی پایگاه داده

### ایجاد پایگاه داده
1. روی آیکون WAMP کلیک کنید و "phpMyAdmin" را انتخاب کنید
2. در صفحه phpMyAdmin، روی "New" کلیک کنید
3. نام پایگاه داده را `cafe_dennis` وارد کنید
4. Collation را روی `utf8mb4_unicode_ci` تنظیم کنید
5. روی "Create" کلیک کنید

### تنظیم کاربر پایگاه داده (اختیاری)
```sql
-- ایجاد کاربر جدید
CREATE USER 'cafe_admin'@'localhost' IDENTIFIED BY 'your_secure_password';

-- اعطای دسترسی‌های لازم
GRANT ALL PRIVILEGES ON cafe_dennis.* TO 'cafe_admin'@'localhost';

-- اعمال تغییرات
FLUSH PRIVILEGES;
```

## مرحله ۳: کپی فایل‌های پروژه

### کپی کردن فایل‌ها
1. فایل‌های پروژه کافه دنیس را در مسیر زیر کپی کنید:
   ```
   C:\wamp64\www\cafe-dennis\
   ```

2. ساختار فولدر باید به شکل زیر باشد:
   ```
   cafe-dennis/
   ├── index.html
   ├── about.html
   ├── menu.php
   ├── css/
   │   └── style.css
   ├── js/
   │   └── script.js
   ├── images/
   │   └── menu/
   ├── config/
   │   └── database.php
   ├── admin/
   │   ├── index.php
   │   ├── login.php
   │   ├── logout.php
   │   ├── add_item.php
   │   ├── get_items.php
   │   ├── delete_item.php
   │   └── get_stats.php
   ├── logs/
   ├── cache/
   └── installation/
   ```

## مرحله ۴: تنظیم پایگاه داده

### ویرایش فایل تنظیمات
1. فایل `config/database.php` را باز کنید
2. تنظیمات پایگاه داده را بررسی کنید:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'cafe_dennis');
define('DB_USER', 'root');  // یا نام کاربری که ایجاد کردید
define('DB_PASS', '');      // یا رمز عبور که تنظیم کردید
```

### تست اتصال پایگاه داده
1. مرورگر را باز کنید و به آدرس زیر بروید:
   ```
   http://localhost/cafe-dennis/menu.php
   ```
2. اگر صفحه بدون خطا بارگذاری شد، اتصال موفق است
3. جداول پایگاه داده به صورت خودکار ایجاد می‌شوند

## مرحله ۵: تنظیم دسترسی‌ها

### تنظیم مجوزهای فولدر
1. روی فولدر `cafe-dennis` کلیک راست کنید
2. "Properties" → "Security" را انتخاب کنید
3. اطمینان حاصل کنید که کاربر `IUSR` دسترسی کامل دارد

### تنظیم PHP
1. فایل `php.ini` را ویرایش کنید (از طریق WAMP icon → PHP → php.ini)
2. تنظیمات زیر را بررسی کنید:

```ini
; حداکثر حجم فایل آپلود (برای تصاویر منو)
upload_max_filesize = 10M
post_max_size = 10M

; فعال‌سازی extension‌های مورد نیاز
extension=pdo_mysql
extension=gd
extension=mbstring

; تنظیمات امنیتی
display_errors = Off
log_errors = On
error_log = "C:\wamp64\logs\php_errors.log"
```

3. Apache را restart کنید

## مرحله ۶: تست نهایی

### بررسی صفحات
1. **صفحه اصلی**: `http://localhost/cafe-dennis/`
2. **صفحه منو**: `http://localhost/cafe-dennis/menu.php`
3. **صفحه درباره ما**: `http://localhost/cafe-dennis/about.html`
4. **پنل مدیریت**: `http://localhost/cafe-dennis/admin/`

### ورود به پنل مدیریت
- **نام کاربری**: `admin`
- **رمز عبور**: `CafeDennis2024!`

### تست عملکردها
1. ورود به پنل مدیریت
2. افزودن محصول جدید با تصویر
3. مشاهده منو در صفحه اصلی
4. تست فیلترینگ محصولات
5. حذف محصول

## مرحله ۷: تنظیمات امنیتی

### تغییر رمز عبور مدیر
1. فایل `config/database.php` را باز کنید
2. خط زیر را پیدا کنید:
```php
define('ADMIN_PASSWORD', password_hash('CafeDennis2024!', PASSWORD_DEFAULT));
```
3. رمز عبور جدید را جایگزین کنید:
```php
define('ADMIN_PASSWORD', password_hash('YOUR_NEW_PASSWORD', PASSWORD_DEFAULT));
```

### محدود کردن دسترسی به فولدرهای حساس
فایل `.htaccess` در فولدر `logs` و `cache` به صورت خودکار ایجاد می‌شود.

## رفع مشکلات رایج

### خطای "Database connection failed"
- بررسی کنید که MySQL در حال اجرا باشد
- تنظیمات پایگاه داده در `config/database.php` را بررسی کنید
- اطمینان حاصل کنید که پایگاه داده `cafe_dennis` ایجاد شده است

### خطای "Permission denied"
- مجوزهای فولدر را بررسی کنید
- اطمینان حاصل کنید که Apache دسترسی نوشتن به فولدرهای `images/menu`, `logs`, `cache` دارد

### صفحه سفید یا خطای 500
- فایل `php_errors.log` را بررسی کنید
- تنظیمات PHP را بررسی کنید
- اطمینان حاصل کنید که تمام extension‌های مورد نیاز فعال هستند

### مشکل در آپلود تصاویر
- حداکثر حجم فایل در `php.ini` را بررسی کنید
- مجوزهای فولدر `images/menu` را بررسی کنید
- فرمت‌های مجاز: JPG, PNG, GIF, WebP

## نکات مهم

1. **پشتیبان‌گیری**: همیشه از پایگاه داده و فایل‌ها پشتیبان بگیرید
2. **امنیت**: رمز عبور پیشفرض را تغییر دهید
3. **بروزرسانی**: WAMP و PHP را به‌روز نگه دارید
4. **لاگ‌ها**: فایل‌های لاگ را به طور منظم بررسی کنید

## پشتیبانی

در صورت بروز مشکل:
1. فایل‌های لاگ را بررسی کنید
2. تنظیمات PHP و Apache را بررسی کنید
3. اطمینان حاصل کنید که تمام فایل‌ها کپی شده‌اند
4. مجوزهای فولدرها را بررسی کنید

---

**موفق باشید! 🎉**

وب‌سایت کافه دنیس شما آماده استفاده است. برای هرگونه سوال یا مشکل، لطفاً مستندات بالا را مطالعه کنید.