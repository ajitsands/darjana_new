<main style="padding: 60px 0 100px; background-color: #fafafa; min-height: 80vh;">
    <div class="container" style="max-width: 1000px;">
        <!-- Page Header -->
        <div style="text-align: center; margin-bottom: 50px;">
            <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.25em; color: var(--color-gold); text-transform: uppercase; display: block; margin-bottom: 8px;">
                CUSTOMER CARE &amp; LEGAL TERMS
            </span>
            <h1 style="font-family: var(--heading-font-family); font-size: 32px; font-weight: 500; letter-spacing: 0.15em; color: #181818; text-transform: uppercase; margin: 0;">
                STORE POLICIES
            </h1>
            <div style="width: 60px; height: 2px; background: var(--color-gold); margin: 16px auto 0;"></div>
        </div>

        <!-- Policy Tabs Container -->
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid #edf2f7; overflow: hidden;">
            
            <!-- Tab Navigation Bar -->
            <div style="display: flex; border-bottom: 1px solid #edf2f7; background: #fff; overflow-x: auto; scrollbar-width: none;">
                <button type="button" class="policy-tab-btn <?= $activeTab === 'shipping' ? 'active' : '' ?>" onclick="switchPolicyTab('shipping', this)">
                    🚚 Shipping &amp; GCC Delivery
                </button>
                <button type="button" class="policy-tab-btn <?= $activeTab === 'returns' ? 'active' : '' ?>" onclick="switchPolicyTab('returns', this)">
                    🔄 Returns &amp; Exchanges
                </button>
                <button type="button" class="policy-tab-btn <?= $activeTab === 'terms' ? 'active' : '' ?>" onclick="switchPolicyTab('terms', this)">
                    📜 Terms &amp; Conditions
                </button>
                <button type="button" class="policy-tab-btn <?= $activeTab === 'privacy' ? 'active' : '' ?>" onclick="switchPolicyTab('privacy', this)">
                    🔒 Privacy Policy
                </button>
            </div>

            <!-- Tab Content Panes -->
            <div style="padding: 40px 45px;" class="policy-body-container">
                
                <!-- 1. Shipping Policy Pane -->
                <div id="tab-shipping" class="policy-pane <?= $activeTab === 'shipping' ? 'active' : '' ?>">
                    <div class="policy-html-content">
                        <?= $settings['shipping_policy'] ?? '<h3>Shipping & GCC Delivery Policy</h3><p>Standard delivery across Bahrain and GCC countries within 3-5 business days via express courier.</p>' ?>
                    </div>
                </div>

                <!-- 2. Returns & Exchanges Pane -->
                <div id="tab-returns" class="policy-pane <?= $activeTab === 'returns' ? 'active' : '' ?>">
                    <div class="policy-html-content">
                        <?= $settings['return_policy'] ?? '<h3>Returns & Exchanges Policy</h3><p>Returns and exchanges are accepted within 7 days of delivery for unworn garments with tags intact.</p>' ?>
                    </div>
                </div>

                <!-- 3. Terms & Conditions Pane -->
                <div id="tab-terms" class="policy-pane <?= $activeTab === 'terms' ? 'active' : '' ?>">
                    <div class="policy-html-content">
                        <?= $settings['terms_conditions'] ?? '<h3>Terms & Conditions</h3><p>By using our website, you agree to comply with our store terms and conditions.</p>' ?>
                    </div>
                </div>

                <!-- 4. Privacy Policy Pane -->
                <div id="tab-privacy" class="policy-pane <?= $activeTab === 'privacy' ? 'active' : '' ?>">
                    <div class="policy-html-content">
                        <?= $settings['privacy_policy'] ?? '<h3>Privacy Policy</h3><p>We respect your privacy and secure all customer information using SSL encryption.</p>' ?>
                    </div>
                </div>

            </div>

            <!-- Agreement Disclaimer Footer -->
            <div style="background: #fafafa; border-top: 1px solid #edf2f7; padding: 18px 45px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; font-size: 13px; color: #718096;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">🛡️</span>
                    <span>By browsing or placing an order on Dar Jana Fashion, you automatically agree to these policies.</span>
                </div>
                <a href="https://wa.me/97333300160" target="_blank" style="color: var(--color-gold); font-weight: 600; text-decoration: none;">
                    Need Help? Contact Concierge &rarr;
                </a>
            </div>

        </div>
    </div>
</main>

<style>
.policy-tab-btn {
    flex: 1;
    min-width: 180px;
    background: none;
    border: none;
    padding: 18px 20px;
    font-size: 13.5px;
    font-weight: 600;
    color: #718096;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.25s ease;
    white-space: nowrap;
    text-align: center;
}
.policy-tab-btn:hover {
    color: #181818;
    background-color: #fafafa;
}
.policy-tab-btn.active {
    color: #181818;
    border-bottom-color: var(--color-gold, #c5a059);
    background-color: #fff;
}
.policy-pane {
    display: none;
}
.policy-pane.active {
    display: block;
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.policy-html-content {
    color: #2d3748;
    line-height: 1.8;
    font-size: 14.5px;
}
.policy-html-content h3 {
    font-family: var(--heading-font-family);
    font-size: 22px;
    font-weight: 500;
    letter-spacing: 0.05em;
    color: #181818;
    margin-top: 0;
    margin-bottom: 16px;
    border-bottom: 1px solid #edf2f7;
    padding-bottom: 12px;
}
.policy-html-content h4 {
    font-size: 16px;
    font-weight: 600;
    color: #2d3748;
    margin-top: 24px;
    margin-bottom: 10px;
}
.policy-html-content p {
    margin-bottom: 14px;
}
.policy-html-content ul, .policy-html-content ol {
    margin-left: 20px;
    margin-bottom: 16px;
}
.policy-html-content li {
    margin-bottom: 6px;
}
@media (max-width: 600px) {
    .policy-body-container {
        padding: 24px 20px !important;
    }
}
</style>

<script>
function switchPolicyTab(tabKey, btn) {
    document.querySelectorAll('.policy-pane').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.policy-tab-btn').forEach(el => el.classList.remove('active'));

    const targetPane = document.getElementById('tab-' + tabKey);
    if (targetPane) {
        targetPane.classList.add('active');
    }
    btn.classList.add('active');

    // Update browser URL state without page reload
    if (window.history && window.history.pushState) {
        window.history.pushState(null, null, '<?= BASE_URL ?>/policies/' + tabKey);
}
</script>
