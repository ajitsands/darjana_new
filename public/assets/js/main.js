/* 
 * Dar Jana Fashion - Frontend JavaScript Logic
 * Handles Cart Drawer, Floating Multi-Currency Switcher (Default Bahrain BHD with 3 Decimals), Mobile Menu Navigation Drawer, Single-Row Sliding Featured Products Carousel, AJAX Operations, Search Modal, and Price Formatting
 */

document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = window.BASE_URL || '';

    // Global Toast Notification Utility
    window.showToast = function(message, isSuccess = true) {
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;';
            document.body.appendChild(toastContainer);
        }
        const toast = document.createElement('div');
        toast.style.background = isSuccess ? '#c6f6d5' : '#fed7d7';
        toast.style.color = isSuccess ? '#2f855a' : '#c53030';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '4px';
        toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        toast.style.fontWeight = 'bold';
        toast.style.fontSize = '14px';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        toast.style.transition = 'all 0.3s ease';
        toast.innerHTML = message;
        
        toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 10);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // Multi-Currency Exchange Rates (Dynamic from Admin Settings if available)
    const siteRates = window.siteCurrencyRates || {};

    const currencies = {
        'BHD': { symbol: 'BHD', name: 'Bahraini Dinar', rate: siteRates['BHD'] || 1.0, decimals: 3, flagSrc: baseUrl + '/assets/images/flags/bh.png' },
        'KWD': { symbol: 'KWD', name: 'Kuwaiti Dinar', rate: siteRates['KWD'] || 0.81, decimals: 3, flagSrc: baseUrl + '/assets/images/flags/kw.png' },
        'SAR': { symbol: 'SAR', name: 'Saudi Riyal', rate: siteRates['SAR'] || 9.95, decimals: 2, flagSrc: baseUrl + '/assets/images/flags/sa.png' },
        'AED': { symbol: 'AED', name: 'UAE Dirham', rate: siteRates['AED'] || 9.76, decimals: 2, flagSrc: baseUrl + '/assets/images/flags/ae.png' },
        'QAR': { symbol: 'QAR', name: 'Qatari Riyal', rate: siteRates['QAR'] || 9.67, decimals: 2, flagSrc: baseUrl + '/assets/images/flags/qa.png' },
        'OMR': { symbol: 'OMR', name: 'Omani Rial', rate: siteRates['OMR'] || 1.02, decimals: 3, flagSrc: baseUrl + '/assets/images/flags/om.png' },
        'USD': { symbol: '$', name: 'US Dollar', rate: siteRates['USD'] || 2.65, decimals: 2, flagSrc: baseUrl + '/assets/images/flags/us.png' },
        'EUR': { symbol: '€', name: 'Euro', rate: siteRates['EUR'] || 2.44, decimals: 2, flagSrc: baseUrl + '/assets/images/flags/eu.png' }
    };

    // Default Currency is Bahrain (BHD)
    let activeCurrency = localStorage.getItem('selectedCurrency') || 'BHD';
    if (!currencies[activeCurrency]) activeCurrency = 'BHD';

    // Set cookie for PHP back-end
    document.cookie = "user_currency=" + activeCurrency + "; path=/; max-age=2592000";

    // Currency Switcher UI Elements
    const currencyTrigger = document.getElementById('currencyFloatingTrigger');
    const currencyPopover = document.getElementById('currencyPopover');
    const activeFlagImg = document.getElementById('activeCurrencyFlagImg');
    const activeCode = document.getElementById('activeCurrencyCode');

    // Toggle Popover from Floating Pill
    function togglePopover(e) {
        e.stopPropagation();
        if (currencyPopover) currencyPopover.classList.toggle('active');
    }

    if (currencyTrigger) currencyTrigger.addEventListener('click', togglePopover);

    document.addEventListener('click', function () {
        if (currencyPopover) currencyPopover.classList.remove('active');
    });

    if (currencyPopover) {
        currencyPopover.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    // Currency Option Items Click Handler
    document.querySelectorAll('.currency-option-item').forEach(item => {
        item.addEventListener('click', function () {
            const code = this.dataset.code;
            if (currencies[code]) {
                activeCurrency = code;
                localStorage.setItem('selectedCurrency', code);
                document.cookie = "user_currency=" + code + "; path=/; max-age=2592000";
                updateCurrencyUI();
                if (currencyPopover) currencyPopover.classList.remove('active');
            }
        });
    });

    function updateCurrencyUI() {
        const curr = currencies[activeCurrency];
        if (activeFlagImg) activeFlagImg.src = curr.flagSrc;
        if (activeCode) activeCode.textContent = curr.symbol;

        // Highlight active item in popover
        document.querySelectorAll('.currency-option-item').forEach(item => {
            if (item.dataset.code === activeCurrency) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        // Convert all prices on page
        convertPricesOnPage();
    }

    window.updateCurrencyUI = updateCurrencyUI;

    function formatPrice(bhdPrice) {
        const curr = currencies[activeCurrency];
        const converted = parseFloat(bhdPrice) * curr.rate;
        const decimals = curr.decimals !== undefined ? curr.decimals : (activeCurrency === 'BHD' ? 3 : 2);
        return converted.toFixed(decimals) + ' ' + curr.symbol;
    }

    window.formatPrice = formatPrice;

    function convertPricesOnPage() {
        // Convert regular prices
        document.querySelectorAll('[data-price-bhd]').forEach(el => {
            const basePrice = el.dataset.priceBhd;
            el.textContent = formatPrice(basePrice);
        });

        // Convert "SAVE X BHD" offer tags
        const curr = currencies[activeCurrency];
        const decimals = curr.decimals !== undefined ? curr.decimals : (activeCurrency === 'BHD' ? 3 : 2);
        document.querySelectorAll('[data-save-bhd]').forEach(el => {
            const baseSave = el.dataset.saveBhd;
            const convertedSave = (parseFloat(baseSave) * curr.rate).toFixed(decimals);
            el.textContent = `SAVE ${convertedSave} ${curr.symbol}`;
        });

        refreshCartDrawer();
    }

    // Mobile Menu Navigation Drawer Logic (< 1200px)
    const mobileNavToggle = document.getElementById('mobileNavToggle');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuDrawer = document.getElementById('mobileMenuDrawer');

    function openMobileMenu() {
        if (mobileMenuOverlay && mobileMenuDrawer) {
            mobileMenuOverlay.classList.add('active');
            mobileMenuDrawer.classList.add('active');
        }
    }

    function closeMobileMenu() {
        if (mobileMenuOverlay && mobileMenuDrawer) {
            mobileMenuOverlay.classList.remove('active');
            mobileMenuDrawer.classList.remove('active');
        }
    }

    if (mobileNavToggle) mobileNavToggle.addEventListener('click', openMobileMenu);
    if (mobileMenuClose) mobileMenuClose.addEventListener('click', closeMobileMenu);
    if (mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', closeMobileMenu);

    document.querySelectorAll('.mobile-nav-link').forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });

    // Single-Row Featured Products Carousel Slider Logic
    const sliderTrack = document.getElementById('featuredSliderTrack');
    const prevBtn = document.getElementById('featuredPrevBtn');
    const nextBtn = document.getElementById('featuredNextBtn');

    if (sliderTrack && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function () {
            const scrollAmount = sliderTrack.clientWidth * 0.75;
            sliderTrack.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function () {
            const scrollAmount = sliderTrack.clientWidth * 0.75;
            sliderTrack.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }

    // Cart Drawer Elements
    const cartDrawerOverlay = document.getElementById('cartDrawerOverlay');
    const cartDrawer = document.getElementById('cartDrawer');
    const cartDrawerTrigger = document.getElementById('cartDrawerTrigger');
    const cartDrawerClose = document.getElementById('cartDrawerClose');
    const cartDrawerItems = document.getElementById('cartDrawerItems');
    const cartDrawerSubtotal = document.getElementById('cartDrawerSubtotal');
    const cartBadgeCount = document.querySelectorAll('.cart-count-badge');

    // Open Cart Drawer
    function openCartDrawer() {
        if (cartDrawerOverlay && cartDrawer) {
            cartDrawerOverlay.classList.add('active');
            cartDrawer.classList.add('active');
            refreshCartDrawer();
        }
    }

    // Close Cart Drawer
    function closeCartDrawer() {
        if (cartDrawerOverlay && cartDrawer) {
            cartDrawerOverlay.classList.remove('active');
            cartDrawer.classList.remove('active');
        }
    }

    if (cartDrawerTrigger) cartDrawerTrigger.addEventListener('click', openCartDrawer);
    if (cartDrawerClose) cartDrawerClose.addEventListener('click', closeCartDrawer);
    if (cartDrawerOverlay) cartDrawerOverlay.addEventListener('click', closeCartDrawer);

    // Refresh Cart Drawer Contents via AJAX
    function refreshCartDrawer() {
        fetch(baseUrl + '/cart/get-json')
            .then(res => res.json())
            .then(data => {
                updateCartCountBadges(data.count);
                renderCartDrawerItems(data.cart, data.total);
            })
            .catch(err => console.error('Error fetching cart:', err));
    }

    function updateCartCountBadges(count) {
        cartBadgeCount.forEach(badge => {
            badge.textContent = count;
        });
    }

    function renderCartDrawerItems(cart, total) {
        if (!cartDrawerItems) return;

        if (cart.length === 0) {
            cartDrawerItems.innerHTML = `
                <div style="text-align: center; padding: 60px 20px;">
                    <p style="color: #666; font-size: 16px; margin-bottom: 20px;">Your cart is currently empty.</p>
                    <a href="${baseUrl}/collections/all-abaya" class="btn-primary" onclick="closeCartDrawer()">Shop Collections</a>
                </div>`;
            if (cartDrawerSubtotal) cartDrawerSubtotal.textContent = formatPrice(0);
            return;
        }

        let html = '';
        cart.forEach(item => {
            const metaParts = [`Code: ${item.product_code}`];
            if (item.color) metaParts.push(`Color: ${item.color}`);
            if (item.size) metaParts.push(`Size: ${item.size}`);
            if (item.length) metaParts.push(`Length: ${item.length}"`);
            
            const noteHtml = item.note ? `<div style="font-size: 11.5px; color: var(--color-accent); font-style: italic; margin-bottom: 6px;">Note: "${item.note}"</div>` : '';

            html += `
                <div class="cart-item" data-key="${item.key}">
                    <img src="${item.image.replace('/high/', '/tiny/')}" alt="${item.name}" class="cart-item-img">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-meta">${metaParts.join(' | ')}</div>
                        ${noteHtml}
                        <div style="font-weight: 700; font-size: 14px; margin-bottom: 8px;">${formatPrice(item.price)}</div>
                        <div class="cart-item-qty-row">
                            <div class="qty-btn-group">
                                <button class="qty-btn btn-qty-minus" data-key="${item.key}" data-qty="${item.quantity - 1}">-</button>
                                <span class="qty-val">${item.quantity}</span>
                                <button class="qty-btn btn-qty-plus" data-key="${item.key}" data-qty="${item.quantity + 1}">+</button>
                            </div>
                            <button class="cart-item-remove" data-key="${item.key}">Remove</button>
                        </div>
                    </div>
                </div>`;
        });

        cartDrawerItems.innerHTML = html;
        if (cartDrawerSubtotal) cartDrawerSubtotal.textContent = formatPrice(total);

        attachCartDrawerEvents();
    }

    function attachCartDrawerEvents() {
        document.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(btn => {
            btn.addEventListener('click', function () {
                const key = this.dataset.key;
                const newQty = parseInt(this.dataset.qty);
                updateCartQuantity(key, newQty);
            });
        });

        document.querySelectorAll('.cart-item-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const key = this.dataset.key;
                removeCartItem(key);
            });
        });
    }

    function updateCartQuantity(key, qty) {
        const formData = new FormData();
        formData.append('cart_key', key);
        formData.append('quantity', qty);

        fetch(baseUrl + '/cart/update', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            updateCartCountBadges(data.count);
            renderCartDrawerItems(Object.values(data.cart), data.total);
        });
    }

    function removeCartItem(key) {
        const formData = new FormData();
        formData.append('cart_key', key);

        fetch(baseUrl + '/cart/remove', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            updateCartCountBadges(data.count);
            renderCartDrawerItems(Object.values(data.cart), data.total);
        });
    }

    // Global Add to Cart & Buy Now Event Handlers
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.classList.contains('add-to-cart-form')) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            const isBuyNow = e.submitter && e.submitter.classList.contains('btn-buy-now');
            if (isBuyNow) {
                formData.append('buy_now', '1');
            }

            fetch(baseUrl + '/cart/add', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                if (data.success) {
                    updateCartCountBadges(data.count);
                    openCartDrawer();
                } else {
                    alert(data.message || 'Error adding to cart.');
                }
            })
            .catch(err => console.error('Add to cart error:', err));
        }
    });

    // Size Selector Chips Logic
    document.querySelectorAll('.size-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.size-chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const targetInput = document.getElementById('selectedSizeInput');
            if (targetInput) {
                targetInput.value = this.dataset.size;
            }
        });
    });

    // Search Modal Logic
    const searchModal = document.getElementById('searchModal');
    const searchTrigger = document.getElementById('searchModalTrigger');
    const searchClose = document.getElementById('searchModalClose');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (searchTrigger && searchModal) {
        searchTrigger.addEventListener('click', () => {
            searchModal.classList.add('active');
            if (searchInput) searchInput.focus();
        });
    }

    if (searchClose && searchModal) {
        searchClose.addEventListener('click', () => {
            searchModal.classList.remove('active');
        });
    }

    if (searchInput && searchResults) {
        let debounceTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`${baseUrl}/search?q=${encodeURIComponent(query)}&ajax=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.products.length === 0) {
                            searchResults.innerHTML = '<p style="color: #666;">No products found.</p>';
                            return;
                        }

                        let html = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 16px;">';
                        data.products.forEach(p => {
                            html += `
                                <a href="${baseUrl}/product/${p.slug}" style="display: flex; flex-direction: column; gap: 8px; padding: 10px; border: 1px solid #eee; text-align: center;">
                                    <img src="${p.image.replace('/high/', '/tiny/')}" style="width: 100%; height: 120px; object-fit: cover;">
                                    <div>
                                        <div style="font-weight: 600; font-size: 12px; text-transform: uppercase;">${p.name}</div>
                                        <div style="color: #c5a059; font-size: 11.5px; margin-top: 2px;">${p.product_code} - ${formatPrice(p.price)}</div>
                                    </div>
                                </a>`;
                        });
                        html += '</div>';
                        searchResults.innerHTML = html;
                    });
            }, 300);
        });
    }

    // Newsletter Submission
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(baseUrl + '/newsletter/subscribe', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                window.showToast(data.message, data.success);
                if (data.success) newsletterForm.reset();
            });
        });
    }

    // Initialize Currency & Cart
    updateCurrencyUI();
    refreshCartDrawer();
});
