<?php
session_start();

if(isset($_SESSION['user_id'])) {
    header('Location: desktop.php');
    exit;
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($username === 'admin' && $password === 'r00t') {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['username'] = 'admin';
        $_SESSION['role'] = 'Administrator';
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        header('Location: desktop.php');
        exit;
    } else {
        $error = 'Ungültige Anmeldedaten';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L.I.P - Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body.login-page {
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5aa0 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .login-container { width: 100%; max-width: 500px; }
        .login-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 50px 40px;
            animation: slideDown 0.5s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .login-header h1 {
            font-size: 40px;
            color: #1e3a5f;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 3px;
        }
        .login-header p {
            color: #666;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .header-line {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #1e3a5f, #00a8e8);
            margin: 0 auto;
            border-radius: 2px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #1a1a1a;
            text-transform: uppercase;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #00a8e8;
            box-shadow: 0 0 0 3px rgba(0, 168, 232, 0.1);
        }
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5aa0 100%);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(30, 58, 95, 0.3);
        }
        .error-message {
            background-color: #ffebee;
            color: #d32f2f;
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 13px;
            border-left: 4px solid #d32f2f;
        }
        .version-badge {
            position: fixed;
            bottom: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>L.I.P</h1>
                <p>Live Interaction Portal</p>
                <div class="header-line"></div>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Benutzername</label>
                    <input type="text" name="username" placeholder="admin" autofocus>
                </div>
                
                <div class="form-group">
                    <label>Passwort</label>
                    <input type="password" name="password" placeholder="r00t">
                </div>
                
                <?php if($error): ?>
                    <div class="error-message">⚠ <?php echo $error; ?></div>
                <?php endif; ?>
                
                <button type="submit" class="btn-login">Anmelden</button>
            </form>
        </div>
    </div>
    <div class="version-badge">v0.5</div>
</body>
</html>