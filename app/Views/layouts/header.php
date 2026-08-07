<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' | Dar Jana Fashion' : 'Dar Jana Fashion | Luxury Abayas & Modest Couture' ?></title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Discover luxury abayas, couture dresses, sets, and blazers at Dar Jana Fashion. Premium GCC delivery to Bahrain, Kuwait, Saudi Arabia, UAE, Qatar, and Oman.">
    <meta name="theme-color" content="#f3f3f3">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/web_logo_menu.png">

    <!-- Fonts & Core Stylesheet -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="<?= BASE_URL ?>/assets/css/style.css" as="style">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <!-- Base URL & Currency Rates definition for JS engines -->
    <?php
    if (!isset($siteSettings)) {
        require_once __DIR__ . '/../../Models/Setting.php';
        $settingModelHelper = new Setting();
        $siteSettings = $settingModelHelper->getAll();
    }
    $topAnnouncementText = $siteSettings['top_announcement_bar'] ?? 'EXPRESS GCC DELIVERY TO BAHRAIN, KUWAIT, KSA, UAE, QATAR & OMAN';
    $currencyRatesConfig = [
        'BHD' => 1.00,
        'KWD' => (float)($siteSettings['currency_rate_kwd'] ?? 0.81),
        'SAR' => (float)($siteSettings['currency_rate_sar'] ?? 9.95),
        'AED' => (float)($siteSettings['currency_rate_aed'] ?? 9.76),
        'QAR' => (float)($siteSettings['currency_rate_qar'] ?? 9.67),
        'OMR' => (float)($siteSettings['currency_rate_omr'] ?? 1.02),
        'USD' => (float)($siteSettings['currency_rate_usd'] ?? 2.65),
        'EUR' => (float)($siteSettings['currency_rate_eur'] ?? 2.44)
    ];
    ?>
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
        window.siteCurrencyRates = <?= json_encode($currencyRatesConfig) ?>;
    </script>
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <span><?= htmlspecialchars($topAnnouncementText) ?></span>
    </div>

    <!-- Main Header Wrapper -->
    <header class="header-wrapper">
    <?php $activeNav = $currentCategory ?? $currentSlug ?? ($product['category_slug'] ?? ''); ?>

        <div class="header-container">
            <div class="site-header">
                
                <!-- Mobile Navigation Hamburger Button (Visible on screens < 1200px) -->
                <button class="mobile-nav-toggle icon-btn" id="mobileNavToggle" aria-label="Open Mobile Menu">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <!-- Site Logo -->
                <a href="<?= BASE_URL ?>/" class="site-logo" title="Dar Jana Fashion Home">
                    <img src="<?= BASE_URL ?>/assets/images/web_logo_menu.png" alt="Dar Jana Fashion">
                </a>

                <!-- Desktop Navigation Menu (Dynamic Category Links) -->
                <nav class="header-nav" aria-label="Main Navigation">
                    <?php
                        require_once __DIR__ . '/../../../core/Database.php';
                        $db = Database::getInstance();
                        $headerCategories = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <ul class="main-nav">
                        <li><a href="<?= BASE_URL ?>/" class="nav-link <?= empty($activeNav) ? 'active' : '' ?>">HOME</a></li>
                        <?php foreach($headerCategories as $cat): ?>
                            <li>
                                <a href="<?= BASE_URL ?>/collections/<?= $cat['slug'] ?>" class="nav-link <?= $activeNav === $cat['slug'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars(strtoupper($cat['name'])) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>

                <!-- Header Actions (English, Account, Search, Cart Drawer) -->
                <div class="header-actions">
                    <?php
                        $currentLang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';
                        $langDisplay = $currentLang === 'ar' ? 'العربية' : 'ENGLISH';
                        $switchLangTo = $currentLang === 'ar' ? 'en' : 'ar';
                    ?>
                    <div style="font-size: 11px; font-weight: 600; letter-spacing: 0.15em; color: var(--color-primary); cursor: pointer; position: relative;" class="desktop-only-action" onclick="window.location.href='<?= BASE_URL ?>/lang/<?= $switchLangTo ?>'">
                        <?= $langDisplay ?> ˅
                    </div>

                    <!-- Search Modal Trigger -->
                    <button class="icon-btn" id="searchModalTrigger" title="Search Products" aria-label="Search">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>

                    <!-- Account / User Login -->
                    <a href="<?= BASE_URL ?>/account" class="icon-btn" title="Account Login" aria-label="Account" style="display: none;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </a>

                    <!-- Cart Drawer Trigger -->
                    <button class="icon-btn" id="cartDrawerTrigger" title="Shopping Bag" aria-label="Cart">
                        <svg width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="cart-count-badge">0</span>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <!-- Slide-Over Mobile Menu Overlay & Drawer (< 1200px) -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
    <div class="mobile-menu-drawer" id="mobileMenuDrawer">
        <div class="mobile-menu-header">
            <img src="<?= BASE_URL ?>/assets/images/web_logo_menu.png" alt="Dar Jana Fashion" style="height: 38px;">
            <button class="icon-btn" id="mobileMenuClose" style="font-size: 22px;">✕</button>
        </div>
        <div class="mobile-menu-body">
            <ul class="mobile-nav-list">
                <li><a href="<?= BASE_URL ?>/" class="mobile-nav-link <?= empty($activeNav) ? 'active' : '' ?>">HOME</a></li>
                <?php foreach($headerCategories as $cat): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/collections/<?= $cat['slug'] ?>" class="mobile-nav-link <?= $activeNav === $cat['slug'] ? 'active' : '' ?>">
                            <?= htmlspecialchars(strtoupper($cat['name'])) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="mobile-menu-footer">
            <div style="font-size: 12px; font-weight: 600; color: var(--color-primary); letter-spacing: 0.15em; cursor: pointer;" onclick="window.location.href='<?= BASE_URL ?>/lang/<?= $switchLangTo ?>'">
                LANGUAGE: <span style="color: var(--color-accent);"><?= $langDisplay ?></span>
            </div>
            <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 6px;">
                Customer Support: +973 3330 0160
            </div>
        </div>
    </div>
