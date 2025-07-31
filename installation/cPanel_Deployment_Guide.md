# راهنمای انتشار کافه دنیس روی cPanel

## پیش‌نیازها

### قبل از شروع
- حساب هاستینگ با پشتیبانی از PHP 8.0+ و MySQL
- دسترسی به cPanel
- فایل‌های پروژه کافه دنیس
- نام دامنه (اختیاری)

### بررسی مشخصات هاست
1. ورود به cPanel
2. بخش "Software" → "Select PHP Version"
3. اطمینان از PHP 8.0 یا بالاتر
4. فعال‌سازی extension‌های مورد نیاز:
   - `pdo_mysql`
   - `gd`
   - `mbstring`
   - `fileinfo`

## مرحله ۱: آماده‌سازی فایل‌ها

### فشرده‌سازی پروژه
1. تمام فایل‌های پروژه را در یک فولدر قرار دهید
2. فایل ZIP ایجاد کنید (نام پیشنهادی: `cafe-dennis.zip`)
3. اطمینان حاصل کنید که ساختار زیر وجود دارد:

```
cafe-dennis.zip
├── index.html
├── about.html
├── menu.php
├── css/
├── js/
├── images/
├── config/
├── admin/
├── logs/
├── cache/
└── installation/
```

## مرحله ۲: آپلود فایل‌ها

### استفاده از File Manager
1. وارد cPanel شوید
2. بخش "Files" → "File Manager" را انتخاب کنید
3. به فولدر `public_html` بروید
4. روی "Upload" کلیک کنید
5. فایل `cafe-dennis.zip` را آپلود کنید
6. پس از آپلود، روی فایل ZIP کلیک راست کرده و "Extract" را انتخاب کنید
7. فایل‌ها را از فولدر `cafe-dennis` به `public_html` منتقل کنید

### استفاده از FTP (روش جایگزین)
```
Host: ftp.yourdomain.com
Username: your-ftp-username
Password: your-ftp-password
Port: 21
```

## مرحله ۳: ایجاد پایگاه داده

### ایجاد Database
1. در cPanel، بخش "Databases" → "MySQL Databases" را انتخاب کنید
2. در قسمت "Create New Database":
   - نام: `cafe_dennis`
   - کلیک روی "Create Database"

### ایجاد کاربر پایگاه داده
1. در قسمت "MySQL Users":
   - Username: `cafe_admin`
   - Password: رمز عبور قوی انتخاب کنید
   - کلیک روی "Create User"

### اتصال کاربر به پایگاه داده
1. در قسمت "Add User to Database":
   - User: `cafe_admin`
   - Database: `cafe_dennis`
   - کلیک روی "Add"
2. تمام دسترسی‌ها را انتخاب کنید
3. "Make Changes" را کلیک کنید

## مرحله ۴: تنظیم فایل پیکربندی

### ویرایش database.php
1. در File Manager، فایل `config/database.php` را باز کنید
2. تنظیمات زیر را بروزرسانی کنید:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_cpanel_username_cafe_dennis');  // معمولاً شامل پیشوند نام کاربری
define('DB_USER', 'your_cpanel_username_cafe_admin');   // معمولاً شامل پیشوند نام کاربری
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration - URL سایت خود را وارد کنید
define('SITE_URL', 'https://yourdomain.com/');
```

### تنظیم مسیرهای آپلود
```php
// Upload Configuration
define('UPLOAD_PATH', './images/menu/');
```

## مرحله ۵: تنظیم مجوزهای فولدر

### تنظیم Permissions
در File Manager:

1. فولدر `images/menu`: مجوز `755`
2. فولدر `logs`: مجوز `755`
3. فولدر `cache`: مجوز `755`
4. فولدر `config`: مجوز `644`

### دستور chmod (در صورت دسترسی SSH)
```bash
chmod 755 images/menu
chmod 755 logs
chmod 755 cache
chmod 644 config/database.php
```

## مرحله ۶: تنظیم امنیتی

### ایجاد فایل .htaccess
در فولدر اصلی، فایل `.htaccess` ایجاد کنید:

```apache
# Security Headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# Hide sensitive files
<Files "*.log">
    Order allow,deny
    Deny from all
</Files>

<Files "config/database.php">
    Order allow,deny
    Deny from all
</Files>

# URL Rewriting (اختیاری)
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^admin/?$ admin/index.php [L]

# Force HTTPS (در صورت وجود SSL)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Compress files
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/json
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

## مرحله ۷: تست و راه‌اندازی

### تست اتصال پایگاه داده
1. به آدرس `https://yourdomain.com/menu.php` بروید
2. اگر صفحه بدون خطا بارگذاری شد، اتصال موفق است
3. جداول به صورت خودکار ایجاد می‌شوند

### تست پنل مدیریت
1. به آدرس `https://yourdomain.com/admin/` بروید
2. با اطلاعات زیر وارد شوید:
   - نام کاربری: `admin`
   - رمز عبور: `CafeDennis2024!`

### تست آپلود تصاویر
1. در پنل مدیریت محصول جدیدی اضافه کنید
2. تصویر آپلود کنید
3. بررسی کنید که تصویر در منو نمایش داده می‌شود

## مرحله ۸: تنظیمات نهایی

### تغییر رمز عبور مدیر
1. فایل `config/database.php` را ویرایش کنید
2. رمز عبور جدید تنظیم کنید:
```php
define('ADMIN_PASSWORD', password_hash('YOUR_NEW_SECURE_PASSWORD', PASSWORD_DEFAULT));
```

### تنظیم SSL (در صورت وجود)
1. در cPanel، بخش "Security" → "SSL/TLS" را انتخاب کنید
2. گواهی SSL را نصب کنید
3. "Force HTTPS Redirect" را فعال کنید

### تنظیم ایمیل خطاها
در `config/database.php`:
```php
// Error reporting email
ini_set('log_errors_max_len', 1024);
ini_set('error_log', 'logs/php_errors.log');
```

## مرحله ۹: بهینه‌سازی عملکرد

### فعال‌سازی Caching
در فایل `.htaccess` تنظیمات cache اضافه شده است.

### بهینه‌سازی تصاویر
1. تصاویر را قبل از آپلود فشرده کنید
2. فرمت WebP را ترجیح دهید
3. حداکثر اندازه: 800x600 پیکسل

### تنظیم Cron Jobs (اختیاری)
برای پاک‌سازی خودکار لاگ‌ها:
```bash
# هر روز ساعت 2 صبح
0 2 * * * find /path/to/logs -name "*.log" -mtime +7 -delete
```

## رفع مشکلات رایج

### خطای "Internal Server Error"
1. فایل `error_logs` در cPanel را بررسی کنید
2. مجوزهای فایل‌ها را بررسی کنید
3. نسخه PHP را بررسی کنید

### خطای اتصال پایگاه داده
1. نام پایگاه داده و کاربر را بررسی کنید (شامل پیشوند)
2. رمز عبور را بررسی کنید
3. Host را از `localhost` به IP سرور تغییر دهید

### مشکل آپلود فایل
1. مجوزهای فولدر `images/menu` را بررسی کنید
2. تنظیمات PHP (upload_max_filesize) را بررسی کنید
3. فضای دیسک هاست را بررسی کنید

### صفحه سفید
1. خطاهای PHP را بررسی کنید
2. extension‌های مورد نیاز را فعال کنید
3. حافظه PHP را افزایش دهید

## نکات امنیتی مهم

### تنظیمات امنیتی اضافی
1. رمز عبور پیچیده برای پایگاه داده
2. بروزرسانی منظم فایل‌ها
3. پشتیبان‌گیری منظم
4. محدود کردن دسترسی IP (در صورت نیاز)

### پشتیبان‌گیری
1. **فایل‌ها**: از طریق File Manager یا FTP
2. **پایگاه داده**: از طریق phpMyAdmin
3. **تنظیمات**: کپی از فایل‌های config

## چک‌لیست نهایی

- [ ] فایل‌ها آپلود شده‌اند
- [ ] پایگاه داده ایجاد شده است
- [ ] تنظیمات database.php درست است
- [ ] مجوزهای فولدرها تنظیم شده‌اند
- [ ] فایل .htaccess ایجاد شده است
- [ ] SSL نصب شده است (در صورت وجود)
- [ ] پنل مدیریت کار می‌کند
- [ ] آپلود تصاویر کار می‌کند
- [ ] رمز عبور مدیر تغییر کرده است
- [ ] پشتیبان‌گیری انجام شده است

---

## پشتیبانی

### منابع مفید
- مستندات cPanel
- لاگ‌های خطا در cPanel
- پشتیبانی هاستینگ

### اطلاعات تماس پشتیبانی
در صورت بروز مشکل:
1. لاگ‌های خطا را بررسی کنید
2. با پشتیبانی هاستینگ تماس بگیرید
3. تنظیمات PHP و MySQL را بررسی کنید

**موفق باشید! 🚀**

وب‌سایت کافه دنیس شما روی اینترنت منتشر شده است!