<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Dar Jana Fashion</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        body { 
            background-color: #f4f5f7; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #4a5568;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #a0aec0;
            box-shadow: 0 0 0 3px rgba(160, 174, 192, 0.2);
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #181818;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-submit:hover { background: #333; }
        .error-msg {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <!-- Full-width dark banner for logo -->
        <div style="background: #18181b; padding: 25px; display: flex; flex-direction: column; align-items: center; border-bottom: 3px solid var(--color-accent);">
            <img src="<?= BASE_URL ?>/assets/images/web_logo_menu.png" alt="Dar Jana Fashion" style="max-height: 70px; width: auto; max-width: 100%; display: block;">
        </div>
        
        <!-- Form Container -->
        <div style="padding: 25px 40px 40px 40px;">
            <div style="font-family: var(--heading-font-family); letter-spacing: 0.1em; color: #718096; font-size: 11px; margin-bottom: 25px; font-weight: 600; text-align: center;">ADMIN PORTAL</div>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-msg">Invalid username or password.</div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/admin/login" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Sign In</button>
        </form>
        </div>
    </div>

</body>
</html>
