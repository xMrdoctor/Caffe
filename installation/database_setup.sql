-- =====================================================
-- Database Setup Script for Cafe Dennis Website
-- =====================================================

-- Create database with proper charset for Persian content
CREATE DATABASE IF NOT EXISTS `cafe_dennis` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `cafe_dennis`;

-- =====================================================
-- Table: menu_items
-- Stores all menu items with categories and images
-- =====================================================

CREATE TABLE IF NOT EXISTS `menu_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'نام محصول',
    `category` VARCHAR(100) NOT NULL COMMENT 'دسته‌بندی محصول',
    `price` DECIMAL(10,2) NOT NULL COMMENT 'قیمت محصول',
    `image` VARCHAR(255) DEFAULT NULL COMMENT 'نام فایل تصویر',
    `description` TEXT DEFAULT NULL COMMENT 'توضیحات محصول (اختیاری)',
    `is_available` BOOLEAN DEFAULT TRUE COMMENT 'در دسترس بودن محصول',
    `sort_order` INT DEFAULT 0 COMMENT 'ترتیب نمایش',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ ایجاد',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'تاریخ بروزرسانی',
    
    -- Indexes for better performance
    INDEX `idx_category` (`category`),
    INDEX `idx_name` (`name`),
    INDEX `idx_available` (`is_available`),
    INDEX `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول محصولات منو';

-- =====================================================
-- Table: admin_sessions
-- Manages admin login sessions securely
-- =====================================================

CREATE TABLE IF NOT EXISTS `admin_sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(128) NOT NULL UNIQUE COMMENT 'شناسه جلسه',
    `user_id` VARCHAR(50) NOT NULL COMMENT 'شناسه کاربر',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ ایجاد',
    `expires_at` TIMESTAMP NOT NULL COMMENT 'تاریخ انقضا',
    `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'آدرس IP',
    `user_agent` TEXT DEFAULT NULL COMMENT 'اطلاعات مرورگر',
    `is_active` BOOLEAN DEFAULT TRUE COMMENT 'فعال بودن جلسه',
    
    -- Indexes for performance
    INDEX `idx_session` (`session_id`),
    INDEX `idx_expires` (`expires_at`),
    INDEX `idx_user` (`user_id`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول جلسات مدیریت';

-- =====================================================
-- Table: activity_logs
-- Logs all admin activities for security
-- =====================================================

CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(50) NOT NULL COMMENT 'شناسه کاربر',
    `action` VARCHAR(100) NOT NULL COMMENT 'نوع عملیات',
    `description` TEXT DEFAULT NULL COMMENT 'شرح عملیات',
    `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'آدرس IP',
    `user_agent` TEXT DEFAULT NULL COMMENT 'اطلاعات مرورگر',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ انجام',
    
    -- Indexes for performance
    INDEX `idx_user` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول لاگ فعالیت‌ها';

-- =====================================================
-- Table: settings
-- Store website settings and configurations
-- =====================================================

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE COMMENT 'کلید تنظیم',
    `setting_value` TEXT DEFAULT NULL COMMENT 'مقدار تنظیم',
    `description` VARCHAR(255) DEFAULT NULL COMMENT 'توضیحات',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'تاریخ بروزرسانی',
    
    -- Index for performance
    INDEX `idx_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='جدول تنظیمات';

-- =====================================================
-- Insert Sample Menu Items
-- =====================================================

INSERT INTO `menu_items` (`name`, `category`, `price`, `description`, `sort_order`) VALUES
-- نوشیدنی‌های گرم
('اسپرسو', 'hot_drinks', 25000, 'قهوه خالص و قوی', 1),
('آمریکانو', 'hot_drinks', 30000, 'اسپرسو با آب گرم', 2),
('کاپوچینو', 'hot_drinks', 35000, 'اسپرسو با شیر و فوم', 3),
('لاته', 'hot_drinks', 40000, 'اسپرسو با شیر داغ', 4),
('موکا', 'hot_drinks', 42000, 'قهوه با شکلات', 5),
('چای ماسالا', 'hot_drinks', 28000, 'چای ادویه‌ای هندی', 6),
('هات چاکلت', 'hot_drinks', 35000, 'شکلات داغ کرمی', 7),

-- نوشیدنی‌های سرد
('آیس کافه', 'cold_drinks', 38000, 'قهوه سرد با یخ', 1),
('فراپه', 'cold_drinks', 42000, 'قهوه یخی شیک شده', 2),
('آیس لاته', 'cold_drinks', 45000, 'لاته سرد', 3),
('کولد برو', 'cold_drinks', 40000, 'قهوه دم سرد', 4),
('آیس چای', 'cold_drinks', 25000, 'چای سرد با طعم‌های مختلف', 5),
('لیموناد', 'cold_drinks', 22000, 'نوشیدنی تازه لیمو', 6),
('اسموتی میوه‌ای', 'cold_drinks', 35000, 'اسموتی طبیعی میوه', 7),

-- استایلیش
('موکا استایلیش', 'stylish', 45000, 'موکا با تزیین ویژه', 1),
('لاته آرت', 'stylish', 48000, 'لاته با نقاشی روی فوم', 2),
('کارامل ماکیاتو', 'stylish', 50000, 'قهوه با سس کارامل', 3),
('وانیل لاته', 'stylish', 47000, 'لاته با طعم وانیل', 4),
('هیزلنات کافه', 'stylish', 46000, 'قهوه با طعم فندق', 5),

-- محصولات ویژه
('قهوه ویژه دنیس', 'special', 55000, 'ترکیب منحصر به فرد کافه دنیس', 1),
('ترکیب طلایی', 'special', 60000, 'بهترین دانه‌های قهوه جهان', 2),
('سیگنیچر بلند', 'special', 58000, 'ترکیب ویژه سرآشپز', 3),
('قهوه تک منشأ', 'special', 65000, 'قهوه از بهترین مزارع', 4),
('دنیس اسپشیال', 'special', 70000, 'محصول ویژه و محدود', 5);

-- =====================================================
-- Insert Default Settings
-- =====================================================

INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('cafe_name', 'کافه دنیس', 'نام کافه'),
('cafe_address', 'فلکه مفید کوچه ۵۶ صدوقی ساختمان اول سمت راست ساختمان پاسارگاد', 'آدرس کافه'),
('instagram_handle', 'cafe__denis', 'اکانت اینستاگرام'),
('phone_number', '', 'شماره تلفن کافه'),
('opening_hours', 'شنبه تا پنج‌شنبه: ۸:۰۰ - ۲۳:۰۰، جمعه: ۱۰:۰۰ - ۲۴:۰۰', 'ساعات کاری'),
('website_title', 'کافه دنیس', 'عنوان وب‌سایت'),
('meta_description', 'کافه دنیس - جایی که طعم اصیل قهوه با فضایی گرم و دوستانه ترکیب می‌شود', 'توضیحات متا'),
('currency', 'تومان', 'واحد پول'),
('tax_rate', '0', 'نرخ مالیات'),
('service_charge', '0', 'هزینه سرویس');

-- =====================================================
-- Create Views for Easy Data Access
-- =====================================================

-- View for active menu items with formatted prices
CREATE OR REPLACE VIEW `v_active_menu` AS
SELECT 
    `id`,
    `name`,
    `category`,
    `price`,
    CONCAT(FORMAT(`price`, 0), ' تومان') AS `formatted_price`,
    `image`,
    `description`,
    `sort_order`,
    CASE 
        WHEN `category` = 'hot_drinks' THEN 'نوشیدنی‌های گرم'
        WHEN `category` = 'cold_drinks' THEN 'نوشیدنی‌های سرد'
        WHEN `category` = 'stylish' THEN 'استایلیش'
        WHEN `category` = 'special' THEN 'محصولات ویژه'
        ELSE `category`
    END AS `category_name`,
    `created_at`,
    `updated_at`
FROM `menu_items` 
WHERE `is_available` = TRUE 
ORDER BY `category`, `sort_order`, `name`;

-- View for menu statistics
CREATE OR REPLACE VIEW `v_menu_stats` AS
SELECT 
    COUNT(*) AS `total_items`,
    SUM(CASE WHEN `category` = 'hot_drinks' THEN 1 ELSE 0 END) AS `hot_drinks`,
    SUM(CASE WHEN `category` = 'cold_drinks' THEN 1 ELSE 0 END) AS `cold_drinks`,
    SUM(CASE WHEN `category` = 'stylish' THEN 1 ELSE 0 END) AS `stylish`,
    SUM(CASE WHEN `category` = 'special' THEN 1 ELSE 0 END) AS `special`,
    AVG(`price`) AS `average_price`,
    MIN(`price`) AS `min_price`,
    MAX(`price`) AS `max_price`
FROM `menu_items` 
WHERE `is_available` = TRUE;

-- =====================================================
-- Create Stored Procedures
-- =====================================================

DELIMITER //

-- Procedure to add new menu item
CREATE PROCEDURE `sp_add_menu_item`(
    IN p_name VARCHAR(255),
    IN p_category VARCHAR(100),
    IN p_price DECIMAL(10,2),
    IN p_image VARCHAR(255),
    IN p_description TEXT,
    IN p_sort_order INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    INSERT INTO `menu_items` 
    (`name`, `category`, `price`, `image`, `description`, `sort_order`) 
    VALUES 
    (p_name, p_category, p_price, p_image, p_description, p_sort_order);
    
    COMMIT;
    
    SELECT LAST_INSERT_ID() AS `item_id`;
END //

-- Procedure to update menu item
CREATE PROCEDURE `sp_update_menu_item`(
    IN p_id INT,
    IN p_name VARCHAR(255),
    IN p_category VARCHAR(100),
    IN p_price DECIMAL(10,2),
    IN p_image VARCHAR(255),
    IN p_description TEXT,
    IN p_sort_order INT,
    IN p_is_available BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    UPDATE `menu_items` 
    SET 
        `name` = p_name,
        `category` = p_category,
        `price` = p_price,
        `image` = p_image,
        `description` = p_description,
        `sort_order` = p_sort_order,
        `is_available` = p_is_available,
        `updated_at` = CURRENT_TIMESTAMP
    WHERE `id` = p_id;
    
    COMMIT;
    
    SELECT ROW_COUNT() AS `affected_rows`;
END //

-- Procedure to delete menu item
CREATE PROCEDURE `sp_delete_menu_item`(
    IN p_id INT
)
BEGIN
    DECLARE v_image VARCHAR(255);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Get image filename before deletion
    SELECT `image` INTO v_image FROM `menu_items` WHERE `id` = p_id;
    
    DELETE FROM `menu_items` WHERE `id` = p_id;
    
    COMMIT;
    
    SELECT ROW_COUNT() AS `affected_rows`, v_image AS `deleted_image`;
END //

-- Procedure to log admin activity
CREATE PROCEDURE `sp_log_activity`(
    IN p_user_id VARCHAR(50),
    IN p_action VARCHAR(100),
    IN p_description TEXT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    INSERT INTO `activity_logs` 
    (`user_id`, `action`, `description`, `ip_address`, `user_agent`) 
    VALUES 
    (p_user_id, p_action, p_description, p_ip_address, p_user_agent);
END //

-- Procedure to clean old sessions
CREATE PROCEDURE `sp_clean_old_sessions`()
BEGIN
    DELETE FROM `admin_sessions` 
    WHERE `expires_at` < NOW() OR `is_active` = FALSE;
    
    SELECT ROW_COUNT() AS `cleaned_sessions`;
END //

-- Procedure to clean old logs (keep last 30 days)
CREATE PROCEDURE `sp_clean_old_logs`()
BEGIN
    DELETE FROM `activity_logs` 
    WHERE `created_at` < DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    SELECT ROW_COUNT() AS `cleaned_logs`;
END //

DELIMITER ;

-- =====================================================
-- Create Triggers
-- =====================================================

-- Trigger to log menu item changes
DELIMITER //

CREATE TRIGGER `tr_menu_item_insert` 
AFTER INSERT ON `menu_items`
FOR EACH ROW
BEGIN
    INSERT INTO `activity_logs` 
    (`user_id`, `action`, `description`, `ip_address`) 
    VALUES 
    ('system', 'MENU_ITEM_CREATED', 
     CONCAT('محصول جدید اضافه شد: ', NEW.name), 
     '127.0.0.1');
END //

CREATE TRIGGER `tr_menu_item_update` 
AFTER UPDATE ON `menu_items`
FOR EACH ROW
BEGIN
    INSERT INTO `activity_logs` 
    (`user_id`, `action`, `description`, `ip_address`) 
    VALUES 
    ('system', 'MENU_ITEM_UPDATED', 
     CONCAT('محصول بروزرسانی شد: ', NEW.name), 
     '127.0.0.1');
END //

CREATE TRIGGER `tr_menu_item_delete` 
AFTER DELETE ON `menu_items`
FOR EACH ROW
BEGIN
    INSERT INTO `activity_logs` 
    (`user_id`, `action`, `description`, `ip_address`) 
    VALUES 
    ('system', 'MENU_ITEM_DELETED', 
     CONCAT('محصول حذف شد: ', OLD.name), 
     '127.0.0.1');
END //

DELIMITER ;

-- =====================================================
-- Create Events for Automatic Cleanup
-- =====================================================

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

-- Event to clean old sessions daily
CREATE EVENT IF NOT EXISTS `ev_clean_sessions`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
  CALL `sp_clean_old_sessions`();

-- Event to clean old logs weekly
CREATE EVENT IF NOT EXISTS `ev_clean_logs`
ON SCHEDULE EVERY 1 WEEK
STARTS CURRENT_TIMESTAMP
DO
  CALL `sp_clean_old_logs`();

-- =====================================================
-- Grant Permissions (if creating specific user)
-- =====================================================

-- Create database user (uncomment if needed)
-- CREATE USER 'cafe_admin'@'localhost' IDENTIFIED BY 'your_secure_password_here';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON cafe_dennis.* TO 'cafe_admin'@'localhost';
-- GRANT EXECUTE ON cafe_dennis.* TO 'cafe_admin'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- Verification Queries
-- =====================================================

-- Check if tables are created
SELECT 
    TABLE_NAME as 'جدول',
    TABLE_ROWS as 'تعداد رکورد',
    TABLE_COMMENT as 'توضیحات'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'cafe_dennis'
ORDER BY TABLE_NAME;

-- Check sample data
SELECT 
    category as 'دسته‌بندی',
    COUNT(*) as 'تعداد محصولات',
    AVG(price) as 'میانگین قیمت'
FROM menu_items 
GROUP BY category;

-- Display success message
SELECT 'پایگاه داده کافه دنیس با موفقیت ایجاد شد! ✅' as 'پیام';

-- =====================================================
-- End of Script
-- =====================================================