<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="SaNDS Lab - www.sandslab.com">
    <meta name="developer" content="Developed by SaNDS Lab (www.sandslab.com)">
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
            position: relative;
            z-index: 100;
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
            align-items: center;
        }
        .admin-nav a {
            color: #a0aec0;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.15s ease;
        }
        .admin-nav a:hover, .admin-nav a.active {
            color: #fff;
        }
        .admin-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        /* User Profile Dropdown Menu */
        .admin-dropdown-wrapper {
            position: relative;
            display: inline-block;
        }
        .admin-user-btn {
            background: #242424;
            border: 1px solid #383838;
            color: #fff;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
            user-select: none;
        }
        .admin-user-btn:hover, 
        .admin-dropdown-wrapper.open .admin-user-btn {
            background: #2e2e2e;
            border-color: #555;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .admin-user-btn .avatar-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c5a880 0%, #a8895e 100%);
            color: #111;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        .admin-user-btn .chevron-icon {
            transition: transform 0.2s ease;
        }
        .admin-dropdown-wrapper.open .admin-user-btn .chevron-icon,
        .admin-dropdown-wrapper:hover .admin-user-btn .chevron-icon {
            transform: rotate(180deg);
        }
        
        .admin-dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 230px;
            background: #1e1e1e;
            border: 1px solid #333333;
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.05);
            padding: 6px 0;
            z-index: 1000;
            display: none;
            animation: adminDropdownFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .admin-dropdown-wrapper:hover .admin-dropdown-menu,
        .admin-dropdown-wrapper.open .admin-dropdown-menu {
            display: block;
        }
        @keyframes adminDropdownFadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .admin-dropdown-header {
            padding: 10px 16px 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #718096;
            border-bottom: 1px solid #2d3748;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .admin-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .admin-dropdown-item:hover {
            background: #2d3748;
            color: #fff;
        }
        .admin-dropdown-item.active {
            background: #2b394e;
            color: #90cdf4;
            font-weight: 600;
        }
        .admin-dropdown-item svg {
            flex-shrink: 0;
            opacity: 0.85;
        }
        .admin-dropdown-item:hover svg {
            opacity: 1;
        }
        .admin-dropdown-divider {
            height: 1px;
            background: #2d3748;
            margin: 6px 0;
        }
        .admin-dropdown-item.logout {
            color: #fc8181;
        }
        .admin-dropdown-item.logout:hover {
            background: #e53e3e;
            color: #fff;
        }
        .admin-dropdown-item.logout:hover svg {
            stroke: #fff;
        }
        
        .admin-main { padding: 40px; padding-top: 20px; margin: 0 auto; width: 100%; box-sizing: border-box; }
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmDelete(event, url, message = 'Are you sure you want to delete this?') {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#718096',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
        return false;
    }

    function confirmDeleteForm(event, form, message = 'Are you sure you want to delete this?') {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#718096',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
    </script>
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
                <li><a href="<?= BASE_URL ?>/admin/products" class="<?= strpos($uri, '/admin/products') !== false || strpos($uri, '/admin/product') !== false ? 'active' : '' ?>">Products</a></li>
                <li><a href="<?= BASE_URL ?>/admin/categories" class="<?= strpos($uri, '/admin/categories') !== false || strpos($uri, '/admin/category') !== false ? 'active' : '' ?>">Categories</a></li>
                <li><a href="<?= BASE_URL ?>/admin/tailoring-units" class="<?= strpos($uri, '/admin/tailoring-units') !== false ? 'active' : '' ?>">Tailoring Units</a></li>
                <li><a href="<?= BASE_URL ?>/admin/orders" class="<?= strpos($uri, '/admin/orders') !== false || strpos($uri, '/admin/order') !== false ? 'active' : '' ?>">Customer Orders</a></li>
                <li><a href="<?= BASE_URL ?>/admin/coupons" class="<?= strpos($uri, '/admin/coupons') !== false ? 'active' : '' ?>">Coupons &amp; Offers</a></li>
                <li><a href="<?= BASE_URL ?>/admin/subscribers" class="<?= strpos($uri, '/admin/subscribers') !== false ? 'active' : '' ?>">Subscribers &amp; Promo</a></li>
                <li><a href="<?= BASE_URL ?>/admin/settings" class="<?= strpos($uri, '/admin/settings') !== false ? 'active' : '' ?>">Store Settings</a></li>
                <li><a href="<?= BASE_URL ?>/collections/all-abaya" target="_blank">View Storefront ↗</a></li>
            </ul>
        </div>
        <div class="admin-actions">
            <?php 
                $isAdminSectionActive = strpos($uri, '/admin/users') !== false || strpos($uri, '/admin/history') !== false;
                $username = $_SESSION['username'] ?? 'Admin';
            ?>
            <div class="admin-dropdown-wrapper" id="adminDropdownWrapper">
                <button type="button" class="admin-user-btn <?= $isAdminSectionActive ? 'active' : '' ?>" id="adminDropdownBtn" aria-haspopup="true" aria-expanded="false">
                    <span class="avatar-badge"><?= strtoupper(substr($username, 0, 1)) ?></span>
                    <span>Welcome, <strong><?= htmlspecialchars($username) ?></strong></span>
                    <svg class="chevron-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="admin-dropdown-menu" id="adminDropdownMenu">
                    <div class="admin-dropdown-header">
                        <span>Account & Security</span>
                    </div>
                    <a href="<?= BASE_URL ?>/admin/users" class="admin-dropdown-item <?= strpos($uri, '/admin/users') !== false ? 'active' : '' ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Manage Admins</span>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/history" class="admin-dropdown-item <?= strpos($uri, '/admin/history') !== false ? 'active' : '' ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 14 14"></polyline>
                        </svg>
                        <span>Activity History</span>
                    </a>
                    <div class="admin-dropdown-divider"></div>
                    <a href="<?= BASE_URL ?>/admin/logout" class="admin-dropdown-item logout">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fc8181" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Log Out</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <script>
    // User Profile Dropdown click toggle support
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('adminDropdownWrapper');
        const btn = document.getElementById('adminDropdownBtn');
        if (wrapper && btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                wrapper.classList.toggle('open');
                btn.setAttribute('aria-expanded', wrapper.classList.contains('open'));
            });
            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    wrapper.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
    </script>

    <main class="admin-main">
