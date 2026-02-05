<?php
// index.php
session_start();
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    header('Location: modules/dashboard.php');
    exit();
}

// Handle login
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db === null) {
            $error = 'Database connection failed. Please try again later.';
        } else {
            $query = "SELECT * FROM users WHERE username = :username AND is_active = 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password_hash'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['user_role'];
                    $_SESSION['employee_id'] = $user['employee_id'];
                    
                    // Update last login
                    $updateQuery = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(':user_id', $user['user_id']);
                    $updateStmt->execute();
                    
                    // Reset login attempts
                    $resetQuery = "UPDATE users SET login_attempts = 0 WHERE user_id = :user_id";
                    $resetStmt = $db->prepare($resetQuery);
                    $resetStmt->bindParam(':user_id', $user['user_id']);
                    $resetStmt->execute();
                    
                    header('Location: modules/dashboard.php');
                    exit();
                } else {
                    $error = 'Invalid username or password';
                    
                    // Increment login attempts
                    $attemptsQuery = "UPDATE users SET login_attempts = login_attempts + 1 WHERE user_id = :user_id";
                    $attemptsStmt = $db->prepare($attemptsQuery);
                    $attemptsStmt->bindParam(':user_id', $user['user_id']);
                    $attemptsStmt->execute();
                    
                    // Lock account after 5 failed attempts
                    $lockQuery = "UPDATE users SET lock_until = DATE_ADD(NOW(), INTERVAL 30 MINUTE) 
                                 WHERE user_id = :user_id AND login_attempts >= 5";
                    $lockStmt = $db->prepare($lockQuery);
                    $lockStmt->bindParam(':user_id', $user['user_id']);
                    $lockStmt->execute();
                }
            } else {
                $error = 'Invalid username or password';
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
        error_log('Login Error: ' . $e->getMessage());
    } catch (Exception $e) {
        $error = 'An error occurred. Please try again.';
        error_log('Login Error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            max-width: 120px;
            margin-bottom: 15px;
        }
        
        .logo h1 {
            color: #2c3e50;
            font-size: 24px;
            margin: 0;
            font-weight: 600;
        }
        
        .logo p {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #c33;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }
        
        .input-icon input {
            padding-left: 45px;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div style="background: linear-gradient(135deg, #667eea, #764ba2); width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-tie" style="color: white; font-size: 32px;"></i>
            </div>
            <h1><?php echo SITE_NAME; ?></h1>
            <p>DepEd Silay City Division</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login to System
            </button>
        </form>
        
        <div class="footer-text">
            <p>© <?php echo date('Y'); ?> DepEd Silay City Division. All rights reserved.</p>
            <p>For technical support, contact IT Department</p>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
        
        // Add enter key support
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>