<?php
require_once '../config/database.php';
initSecureSession();

// Check if already logged in
if (checkAdminAuth()) {
    header('Location: index.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting
    if (!checkRateLimit('login', 5, 300)) {
        $error_message = 'تعداد تلاش‌های ناموفق زیاد است. لطفا ۵ دقیقه صبر کنید.';
        logSecurityEvent('LOGIN_RATE_LIMIT', 'Too many login attempts');
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validate CSRF token
        if (!validateCSRF($csrf_token)) {
            $error_message = 'درخواست نامعتبر است.';
            logSecurityEvent('LOGIN_CSRF_FAIL', 'Invalid CSRF token');
        } else {
            // Check credentials
            if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD)) {
                // Successful login
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['last_activity'] = time();
                
                logSecurityEvent('LOGIN_SUCCESS', 'Admin login successful');
                header('Location: index.php');
                exit;
            } else {
                $error_message = 'نام کاربری یا رمز عبور اشتباه است.';
                logSecurityEvent('LOGIN_FAIL', 'Invalid credentials for username: ' . $username);
            }
        }
    }
}

$csrf_token = generateCSRF();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود مدیر - کافه دنیس</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-coffee"></i>
                    <span>کافه دنیس</span>
                </div>
                <h2>ورود مدیریت</h2>
            </div>
            
            <?php if ($error_message): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="username">نام کاربری</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    <i class="fas fa-user"></i>
                </div>
                
                <div class="form-group">
                    <label for="password">رمز عبور</label>
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-lock"></i>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    ورود
                </button>
            </form>
            
            <div class="login-footer">
                <a href="../index.html">
                    <i class="fas fa-arrow-right"></i>
                    بازگشت به سایت
                </a>
            </div>
        </div>
    </div>
    
    <style>
    body {
        background: linear-gradient(135deg, #8b4513, #a0522d);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 20px;
    }
    
    .login-container {
        width: 100%;
        max-width: 400px;
    }
    
    .login-box {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.5s ease;
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .login-header .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #8b4513;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .login-header .logo i {
        color: #d4a574;
        font-size: 2rem;
    }
    
    .login-header h2 {
        color: #8b4513;
        font-weight: 600;
        margin: 0;
    }
    
    .error-message {
        background: #ffe6e6;
        color: #d63384;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid #f5c2c7;
    }
    
    .login-form {
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        color: #8b4513;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .form-group input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-family: 'Vazirmatn', sans-serif;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus {
        border-color: #d4a574;
        outline: none;
        box-shadow: 0 0 10px rgba(212, 165, 116, 0.3);
    }
    
    .form-group i {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #8b4513;
        margin-top: 12px;
    }
    
    .login-btn {
        width: 100%;
        background: linear-gradient(45deg, #d4a574, #cd853f);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Vazirmatn', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .login-btn:hover {
        background: linear-gradient(45deg, #cd853f, #d4a574);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(212, 165, 116, 0.4);
    }
    
    .login-footer {
        text-align: center;
        padding-top: 1rem;
        border-top: 1px solid #e0e0e0;
    }
    
    .login-footer a {
        color: #8b4513;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .login-footer a:hover {
        color: #d4a574;
        transform: translateX(5px);
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 480px) {
        .login-box {
            padding: 1.5rem;
        }
        
        .login-header .logo {
            font-size: 1.3rem;
        }
        
        .login-header .logo i {
            font-size: 1.8rem;
        }
    }
    </style>
</body>
</html>