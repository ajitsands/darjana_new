<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Dashboard | Dar Jana Fashion' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        body { background-color: #f4f5f7; margin: 0; }
        .admin-header {
            background: #181818;
            color: #fff;
            padding: 0 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }
        .admin-logo {
            margin-right: 40px;
            display: flex;
            align-items: center;
        }
        .admin-logo img {
            max-height: 40px;
            width: auto;
        }
        .admin-nav {
            display: flex;
            gap: 25px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .admin-nav a {
            color: #a0aec0;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        .admin-nav a:hover, .admin-nav a.active {
            color: #fff;
        }
        .admin-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .admin-actions span {
            color: #a0aec0;
            font-size: 13px;
        }
        .admin-actions a.logout-btn {
            background: #e53e3e;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }
        .admin-actions a.logout-btn:hover { background: #c53030; }
        
        .admin-main { padding: 40px; margin: 0 auto; width: 100%; box-sizing: border-box; }
        .stat-card { background: #fff; border-radius: 6px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table-responsive { background: #fff; border-radius: 6px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 16px; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        th { font-family: var(--heading-font-family); font-size: 11px; letter-spacing: 0.12em; color: #718096; background: #f8fafc; }
        
        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; color: #4a5568; margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 4px; font-family: inherit; font-size: 14px; box-sizing: border-box;
        }
        .btn-primary { background: #181818; color: #fff; border: none; padding: 12px 24px; border-radius: 4px; font-weight: 600; cursor: pointer; }
        .btn-primary:hover { background: #000; }
    </style>
</head>
<body>

    <header class="admin-header">
        <div style="display: flex; align-items: center;">
            <div class="admin-logo">
                <img src="<?= BASE_URL ?>/assets/images/web_logo_menu.png" alt="Dar Jana Fashion">
            </div>
            <ul class="admin-nav">
                <?php 
                    $uri = $_SERVER['REQUEST_URI'] ?? '';
                    $isAdminHome = rtrim($uri, '/') == '/admin' || strpos($uri, '?') !== false && strpos($uri, '/admin?') === 0;
                ?>
                <li><a href="<?= BASE_URL ?>/admin" class="<?= $isAdminHome ? 'active' : '' ?>">Dashboard</a></li>
                <li><a href="<?= BASE_URL ?>/admin/users" class="<?= strpos($uri, '/admin/users') !== false ? 'active' : '' ?>">Manage Admins</a></li>
                <li><a href="<?= BASE_URL ?>/admin/history" class="<?= strpos($uri, '/admin/history') !== false ? 'active' : '' ?>">Activity History</a></li>
                <li><a href="<?= BASE_URL ?>/admin/orders" class="<?= strpos($uri, '/admin/orders') !== false ? 'active' : '' ?>">Customer Orders</a></li>
                <li><a href="<?= BASE_URL ?>/collections/all-abaya" target="_blank">View Storefront ↗</a></li>
            </ul>
        </div>
        <div class="admin-actions">
            <span>Welcome, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></strong></span>
            <a href="<?= BASE_URL ?>/admin/logout" class="logout-btn">Log Out</a>
        </div>
    </header>

    <main class="admin-main">
