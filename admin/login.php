<?php
require_once '../config.php';

// If a user is already logged in, redirect them to the admin dashboard.
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error_message = '';

// Handle the form submission when the user clicks "Login".
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($username) || empty($password)) {
        $error_message = 'نام کاربری و رمز عبور الزامی است.';
    } else {
        // Prepare a statement to prevent SQL injection.
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the submitted password against the hash stored in the database.
            if (password_verify($password, $user['password'])) {
                // Password is correct. Start a session.
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                header('Location: index.php'); // Redirect to the main admin page
                exit;
            }
        }

        // If the user is not found or password doesn't match
        $error_message = 'نام کاربری یا رمز عبور اشتباه است.';
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به پنل مدیریت</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Admin-specific styles for the login page -->
    <style>
        :root {
            --admin-bg: #f4f7f6;
            --login-panel-bg: #ffffff;
            --admin-primary: #4B3621;
            --admin-accent: #8B5E34;
        }
        body {
            background-color: var(--admin-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Vazirmatn', sans-serif;
        }
        .login-panel {
            background: var(--login-panel-bg);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-panel h1 {
            margin-bottom: 25px;
            color: var(--admin-primary);
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-family: var(--font-primary);
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: var(--admin-accent);
            outline: none;
        }
        .error {
            color: #d9534f;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .btn-admin {
            background: var(--admin-accent);
            color: #fff;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-admin:hover {
            background-color: var(--admin-primary);
        }
    </style>
</head>
<body>
    <div class="login-panel">
        <h1>ورود به پنل مدیریت</h1>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST" novalidate>
            <div class="form-group">
                <label for="username">نام کاربری:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">رمز عبور:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-admin">ورود</button>
        </form>
    </div>
</body>
</html>
