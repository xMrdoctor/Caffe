<?php
require_once '../config/database.php';
initSecureSession();

// Check authentication
if (!checkAdminAuth()) {
    header('Location: login.php');
    exit;
}

// Initialize database
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die('خطا در اتصال به پایگاه داده');
}

$database->createTables();
$csrf_token = generateCSRF();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت - کافه دنیس</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>
                <i class="fas fa-cogs"></i>
                پنل مدیریت کافه دنیس
            </h1>
            <div class="admin-nav">
                <span>خوش آمدید، <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    خروج
                </a>
            </div>
        </div>

        <!-- Add Item Form -->
        <div class="admin-form">
            <h2>افزودن محصول جدید</h2>
            <form id="addItemForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="itemName">نام محصول *</label>
                        <input type="text" id="itemName" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="itemCategory">دسته‌بندی *</label>
                        <select id="itemCategory" name="category" required>
                            <option value="">انتخاب کنید</option>
                            <option value="hot_drinks">نوشیدنی‌های گرم</option>
                            <option value="cold_drinks">نوشیدنی‌های سرد</option>
                            <option value="stylish">استایلیش</option>
                            <option value="special">محصولات ویژه</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="itemPrice">قیمت (تومان) *</label>
                        <input type="number" id="itemPrice" name="price" required min="0" step="1000">
                    </div>
                    
                    <div class="form-group">
                        <label for="itemImage">تصویر محصول</label>
                        <input type="file" id="itemImage" name="image" accept="image/*">
                    </div>
                </div>
                
                <div class="form-group">
                    <div id="imagePreview">
                        <p>پیش‌نمایش تصویر</p>
                    </div>
                </div>
                
                <button type="submit" class="admin-btn">
                    <i class="fas fa-plus"></i>
                    افزودن محصول
                </button>
            </form>
        </div>

        <!-- Menu Items List -->
        <div class="admin-section">
            <h2>لیست محصولات</h2>
            <div class="table-container">
                <table class="admin-table" id="menuItemsTable">
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام</th>
                            <th>دسته‌بندی</th>
                            <th>قیمت</th>
                            <th>تاریخ ایجاد</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistics -->
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalItems">0</h3>
                    <p>کل محصولات</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="stat-content">
                    <h3 id="hotDrinks">0</h3>
                    <p>نوشیدنی گرم</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-snowflake"></i>
                </div>
                <div class="stat-content">
                    <h3 id="coldDrinks">0</h3>
                    <p>نوشیدنی سرد</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 id="specialItems">0</h3>
                    <p>محصولات ویژه</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-actions">
            <h2>عملیات سریع</h2>
            <div class="action-buttons">
                <a href="../menu.php" target="_blank" class="action-btn">
                    <i class="fas fa-eye"></i>
                    مشاهده منو
                </a>
                <a href="../index.html" target="_blank" class="action-btn">
                    <i class="fas fa-home"></i>
                    مشاهده سایت
                </a>
                <button onclick="exportMenu()" class="action-btn">
                    <i class="fas fa-download"></i>
                    دانلود منو
                </button>
                <button onclick="clearCache()" class="action-btn">
                    <i class="fas fa-refresh"></i>
                    پاک کردن کش
                </button>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
    
    <style>
    .admin-nav {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: white;
    }
    
    .logout-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 15px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .logout-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .admin-section {
        background: #faf8f5;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
    
    .admin-section h2 {
        color: #8b4513;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .admin-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(139, 69, 19, 0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(139, 69, 19, 0.2);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(45deg, #d4a574, #cd853f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }
    
    .stat-content h3 {
        color: #8b4513;
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    
    .stat-content p {
        color: #666;
        margin: 0;
        font-size: 0.9rem;
    }
    
    .admin-actions {
        background: #faf8f5;
        padding: 2rem;
        border-radius: 15px;
    }
    
    .admin-actions h2 {
        color: #8b4513;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .action-btn {
        background: linear-gradient(45deg, #d4a574, #cd853f);
        color: white;
        text-decoration: none;
        padding: 1rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-family: 'Vazirmatn', sans-serif;
    }
    
    .action-btn:hover {
        background: linear-gradient(45deg, #cd853f, #d4a574);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(212, 165, 116, 0.4);
    }
    
    #imagePreview {
        background: #f8f9fa;
        border: 2px dashed #d4a574;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        color: #666;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .admin-stats {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            grid-template-columns: 1fr;
        }
        
        .admin-nav {
            flex-direction: column;
            gap: 0.5rem;
            text-align: center;
        }
    }
    </style>
    
    <script>
    // Additional admin functions
    function exportMenu() {
        fetch('export_menu.php')
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'cafe-dennis-menu.json';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            showMessage('منو با موفقیت دانلود شد', 'success');
        })
        .catch(error => {
            showMessage('خطا در دانلود منو', 'error');
        });
    }
    
    function clearCache() {
        fetch('clear_cache.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                csrf_token: '<?php echo $csrf_token; ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('کش با موفقیت پاک شد', 'success');
            } else {
                showMessage('خطا در پاک کردن کش', 'error');
            }
        })
        .catch(error => {
            showMessage('خطا در ارتباط با سرور', 'error');
        });
    }
    
    // Update statistics
    function updateStats() {
        fetch('get_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalItems').textContent = data.stats.total;
                document.getElementById('hotDrinks').textContent = data.stats.hot_drinks;
                document.getElementById('coldDrinks').textContent = data.stats.cold_drinks;
                document.getElementById('specialItems').textContent = data.stats.special;
            }
        })
        .catch(error => {
            console.error('Error updating stats:', error);
        });
    }
    
    // Initialize stats on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateStats();
        // Refresh stats every 30 seconds
        setInterval(updateStats, 30000);
    });
    </script>
</body>
</html>