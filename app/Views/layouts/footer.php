    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <h3 style="font-family: var(--heading-font-family); font-size: 22px; font-weight: 500; letter-spacing: 0.2em; color: var(--color-primary);">BE THE FIRST TO KNOW</h3>
            <p style="color: var(--color-text-muted); font-size: 14.5px; margin-top: 6px;">Subscribe to receive updates on new couture collections and exclusive private invitations.</p>
            <form class="newsletter-form" id="newsletterForm">
                <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address" required>
                <button type="submit" class="btn-primary" style="padding: 14px 28px; font-size: 11px;">SUBSCRIBE</button>
            </form>
        </div>
    </section>

    <!-- Footer - Main Columns Left Aligned, Copyright Text Center Aligned -->
    <footer class="site-footer">
        <div class="full-width-container">
            <div class="footer-grid">
                <!-- Column 1: Brand Info (Left Aligned) -->
                <div class="footer-column" style="text-align: left;">
                    <div style="margin-bottom: 20px;">
                        <img src="<?= BASE_URL ?>/assets/images/web_logo_menu.png" alt="Dar Jana Fashion" style="height: 52px; object-fit: contain;">
                    </div>
                    <p style="color: #64748b; font-size: 14px; line-height: 1.7; max-width: 380px; margin-bottom: 20px;">
                        Dar Jana Fashion represents luxury, elegance, and modern modest couture. Designing exclusive abayas, sets, and luxury blazers across the GCC region.
                    </p>
                    <p style="color: #64748b; font-size: 13px;">
                        <strong>Customer Support:</strong> +973 3330 0160<br>
                        <strong>Email:</strong> care@darjanafashion.com
                    </p>
                </div>

                <!-- Column 2: Quick Links (Left Aligned) -->
                <div class="footer-column" style="text-align: left;">
                    <h4>SHOP COLLECTIONS</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/collections/all-abaya">All Abaya</a></li>
                        <li><a href="<?= BASE_URL ?>/collections/black-abaya">Black Abaya</a></li>
                        <li><a href="<?= BASE_URL ?>/collections/colourful-abaya">Colourful Abaya</a></li>
                        <li><a href="<?= BASE_URL ?>/collections/sets">Sets & Suits</a></li>
                        <li><a href="<?= BASE_URL ?>/collections/blazer">Blazers</a></li>
                        <li><a href="<?= BASE_URL ?>/collections/ramadan-collection">Ramadan Collection</a></li>
                    </ul>
                </div>

                <!-- Column 3: Customer Care (Left Aligned) -->
                <div class="footer-column" style="text-align: left;">
                    <h4>CUSTOMER CARE</h4>
                    <ul class="footer-links">
                        <li><a href="#">Size Guide</a></li>
                        <li><a href="#">Shipping & GCC Delivery</a></li>
                        <li><a href="#">Returns & Exchanges</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="<?= BASE_URL ?>/track-order">Track Order</a></li>
                    </ul>
                </div>

                <!-- Column 4: Contact & Social (Left Aligned) -->
                <div class="footer-column" style="text-align: left;">
                    <h4>FOLLOW OUR JOURNEY</h4>
                    <p style="color: #64748b; font-size: 13.5px; margin-bottom: 16px;">
                        Connect with us on our social platforms for daily styling inspiration and behind-the-scenes.
                    </p>
                    <div style="display: flex; gap: 14px; margin-top: 10px;">
                        <a href="https://wa.me/97333300160" target="_blank" style="width: 38px; height: 38px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; color: #fff;" title="WhatsApp">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/dar_jana_?igsh=b3VvY3hpejJ0aDZi" target="_blank" style="width: 38px; height: 38px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; color: #fff;" title="Instagram">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="https://www.tiktok.com/@dar_jana_?_r=1&_t=ZS-98a0TvcKGOb" target="_blank" style="width: 38px; height: 38px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; color: #fff;" title="TikTok">
                            <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 3 15.68a6.34 6.34 0 0 0 10.86 4.49A6.27 6.27 0 0 0 15.86 16v-7a8.2 8.2 0 0 0 4.73 1.5v-3.81a4.8 4.8 0 0 1-1-.05z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- EXCLUSIVELY CENTER ALIGNED CLEAN COPYRIGHT TEXT -->
            <div class="footer-bottom" style="justify-content: center; text-align: center;">
                <div>&copy; <?= date('Y') ?> Dar Jana Fashion. All rights reserved.</div>
            </div>
        </div>
    </footer>

    <!-- Interactive Floating Country Multi-Currency Switcher Pill (Fixed Bottom-Left Corner) -->
    <div class="currency-floating-trigger" id="currencyFloatingTrigger" title="Click to Change Currency">
        <img src="<?= BASE_URL ?>/assets/images/flags/bh.png" alt="Bahrain Flag" class="currency-flag-img" loading="lazy" width="20" height="15" id="activeCurrencyFlagImg">
        <span id="activeCurrencyCode">BHD</span>
        <span style="font-size: 10px; color: #64748b;">▴</span>
    </div>

    <!-- Currency Selector Popover Menu -->
    <div class="currency-popover" id="currencyPopover">
        <div style="padding: 12px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: #64748b; border-bottom: 1px solid #eee; background-color: #fafafa;">
            SELECT YOUR CURRENCY
        </div>
        <div class="currency-option-item active" data-code="BHD">
            <img src="<?= BASE_URL ?>/assets/images/flags/bh.png" alt="Bahrain" class="currency-flag-img" loading="lazy" width="20" height="15">
            <span>Bahraini Dinar</span>
            <span class="currency-code-tag">BHD</span>
        </div>
        <div class="currency-option-item" data-code="KWD">
            <img src="<?= BASE_URL ?>/assets/images/flags/kw.png" alt="Kuwait" class="currency-flag-img" loading="lazy" width="20" height="15">
            <span>Kuwaiti Dinar</span>
            <span class="currency-code-tag">KWD</span>
        </div>
        <div class="currency-option-item" data-code="SAR">
            <img src="<?= BASE_URL ?>/assets/images/flags/sa.png" alt="Saudi Arabia" class="currency-flag-img" loading="lazy" width="20" height="15">
            <span>Saudi Riyal</span>
            <span class="currency-code-tag">SAR</span>
        </div>
        <div class="currency-option-item" data-code="AED">
            <img src="<?= BASE_URL ?>/assets/images/flags/ae.png" alt="UAE" class="currency-flag-img" loading="lazy" width="20" height="15">
            <span>UAE Dirham</span>
            <span class="currency-code-tag">AED</span>
        </div>
        <div class="currency-option-item" data-code="QAR">
            <img src="<?= BASE_URL ?>/assets/images/flags/qa.png" alt="Qatar" class="currency-flag-img" loading="lazy" width="20" height="15">
            <span>Qatari Riyal</span>
            <span class="currency-code-tag">QAR</span>
        </div>
        <div class="currency-option-item" data-code="OMR">
            <img src="<?= BASE_URL ?>/assets/images/flags/om.png" alt="Oman" class="currency-flag-img" loading="lazy" width="20" height="15">
            <span>Omani Rial</span>
            <span class="currency-code-tag">OMR</span>
        </div>
        <div class="currency-option-item" data-code="USD">
            <img src="<?= BASE_URL ?>/assets/images/flags/us.png" alt="United States" class="currency-flag-img" loading="lazy" width="20" height="15">
            <span>US Dollar</span>
            <span class="currency-code-tag">$</span>
        </div>
        <div class="currency-option-item" data-code="EUR">
            <img src="<?= BASE_URL ?>/assets/images/flags/eu.png" alt="Eurozone" class="currency-flag-img" loading="lazy" width="20" height="15">
            <span>Euro</span>
            <span class="currency-code-tag">€</span>
        </div>
    </div>

    <!-- Floating Social Bar (Fixed Right Side Middle of Screen) -->
    <div class="floating-social-bar">
        <a href="https://wa.me/97333300160" target="_blank" class="social-float-btn whatsapp" title="Chat on WhatsApp (+973 33300160)">
            <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
        </a>
        <a href="https://www.instagram.com/dar_jana_?igsh=b3VvY3hpejJ0aDZi" target="_blank" class="social-float-btn instagram" title="Follow on Instagram (@dar_jana_)">
            <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </a>
        <a href="https://www.tiktok.com/@dar_jana_?_r=1&_t=ZS-98a0TvcKGOb" target="_blank" class="social-float-btn tiktok" title="Follow on TikTok (@dar_jana_)">
            <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 3 15.68a6.34 6.34 0 0 0 10.86 4.49A6.27 6.27 0 0 0 15.86 16v-7a8.2 8.2 0 0 0 4.73 1.5v-3.81a4.8 4.8 0 0 1-1-.05z"/></svg>
        </a>
    </div>

    <!-- Slide-Over Shopping Cart Drawer -->
    <div class="cart-drawer-overlay" id="cartDrawerOverlay"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer-header">
            <h3 class="cart-drawer-title">YOUR SHOPPING BAG</h3>
            <button class="icon-btn" id="cartDrawerClose" style="font-size: 22px;">✕</button>
        </div>
        <div class="cart-drawer-body" id="cartDrawerItems">
            <!-- Dynamic Cart Items via AJAX -->
        </div>
        <div class="cart-drawer-footer">
            <div class="cart-subtotal-row">
                <span>Subtotal</span>
                <span id="cartDrawerSubtotal" data-price-bhd="0.00">0.00 BHD</span>
            </div>
            <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 16px;">Taxes and shipping calculated at checkout.</p>
            <a href="<?= BASE_URL ?>/checkout" class="btn-primary" style="display: block; width: 100%;">PROCEED TO CHECKOUT</a>
        </div>
    </div>

    <!-- Search Overlay Modal -->
    <div class="search-modal" id="searchModal">
        <button class="search-modal-close" id="searchModalClose">✕</button>
        <div class="search-input-wrap">
            <input type="text" id="searchInput" class="search-input" placeholder="Search for abayas, couture, or codes..." autocomplete="off">
        </div>
        <div class="container" id="searchResults">
            <!-- Real-time AJAX Search Results -->
        </div>
    </div>

    <!-- Core Main JS Engine -->
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
