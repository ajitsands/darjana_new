<?php include __DIR__ . '/header.php'; ?>
<div class="admin-main">
    <!-- Page Title & Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 26px; margin: 0;">Store Settings</h1>
            <p style="color: #718096; font-size: 14px; margin-top: 4px;">Configure store preferences, size guide, payment gateway, policies &amp; header banners</p>
        </div>
    </div>

    <?php if (isset($_SESSION['admin_success'])): ?>
        <div style="background-color: #f0fdf4; color: #16a34a; padding: 14px 18px; border-radius: 6px; margin-bottom: 24px; border: 1px solid #bbf7d0; font-weight: 600;">
            ✅ <?= htmlspecialchars($_SESSION['admin_success']) ?>
            <?php unset($_SESSION['admin_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['admin_error'])): ?>
        <div style="background-color: #fef2f2; color: #dc2626; padding: 14px 18px; border-radius: 6px; margin-bottom: 24px; border: 1px solid #fecaca; font-weight: 600;">
            ❌ <?= htmlspecialchars($_SESSION['admin_error']) ?>
            <?php unset($_SESSION['admin_error']); ?>
        </div>
    <?php endif; ?>

    <!-- TAB NAVIGATION BAR -->
    <div style="border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; display: flex; gap: 10px; flex-wrap: wrap; background: #fff; padding: 10px 10px 0; border-radius: 8px 8px 0 0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <button type="button" class="settings-tab-btn active" onclick="switchSettingsTab('generalTab', this)">
            ⚙️ General Settings
        </button>
        <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('currencyTab', this)">
            💱 Currency Exchange Rates
        </button>
        <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('sizeguideTab', this)">
            📐 Size Guide
        </button>
        <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('paymentTab', this)">
            💳 Payment Gateway Config
        </button>
        <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('policiesTab', this)">
            📜 Policies &amp; Legal Terms
        </button>
        <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('headerTab', this)">
            📢 Website Header &amp; Banners
        </button>
    </div>

    <style>
        .settings-tab-btn {
            background: none;
            border: none;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #718096;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
            margin-bottom: -2px;
            border-radius: 4px 4px 0 0;
        }
        .settings-tab-btn:hover {
            color: #2b6cb0;
            background: #f7fafc;
        }
        .settings-tab-btn.active {
            color: #2b6cb0;
            border-bottom-color: #2b6cb0;
            background: #ebf8ff;
        }
        .settings-pane {
            display: none;
        }
        .settings-pane.active {
            display: block;
            animation: fadeIn 0.25s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <form method="POST" action="<?= BASE_URL ?>/admin/settings" id="settingsForm" enctype="multipart/form-data">

        <!-- TAB 1: GENERAL SETTINGS -->
        <div id="generalTab" class="settings-pane active">
            <div style="background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; width: 100%; box-sizing: border-box;">
                <h2 style="font-size: 18px; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px; color: #2d3748;">
                    ⚙️ General Store Settings
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 24px;">
                    <!-- Timezone -->
                    <div class="form-group">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">Timezone</label>
                        <p style="font-size: 12px; color: #718096; margin-bottom: 8px;">Sets the default time for order creation and click tracking.</p>
                        <select name="timezone" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                            <?php
                            $tzs = [
                                'Asia/Bahrain' => 'Bahrain',
                                'Asia/Kuwait' => 'Kuwait',
                                'Asia/Riyadh' => 'Saudi Arabia (Riyadh)',
                                'Asia/Dubai' => 'UAE (Dubai)',
                                'Asia/Qatar' => 'Qatar',
                                'Asia/Muscat' => 'Oman (Muscat)'
                            ];
                            $currentTz = $settings['timezone'] ?? 'Asia/Bahrain';
                            foreach ($tzs as $val => $label) {
                                $sel = ($val === $currentTz) ? 'selected' : '';
                                echo "<option value=\"$val\" $sel>$label ($val)</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- VAT Percentage -->
                    <div class="form-group">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">VAT Percentage (%)</label>
                        <p style="font-size: 12px; color: #718096; margin-bottom: 8px;">Standard tax percentage calculated on checkout.</p>
                        <input type="number" name="vat_percentage" step="0.1" min="0" value="<?= htmlspecialchars($settings['vat_percentage'] ?? '5') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    </div>

                    <!-- VAT Calculation Type -->
                    <div class="form-group">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">VAT Calculation Type</label>
                        <p style="font-size: 12px; color: #718096; margin-bottom: 8px;">
                            <strong>Inclusive:</strong> Prices include VAT.<br>
                            <strong>Exclusive:</strong> VAT added at checkout.<br>
                            <strong>Without VAT:</strong> No VAT calculated.
                        </p>
                        <select name="vat_type" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                            <option value="none" <?= ($settings['vat_type'] ?? '') === 'none' ? 'selected' : '' ?>>Without VAT (None)</option>
                            <option value="inclusive" <?= ($settings['vat_type'] ?? '') === 'inclusive' ? 'selected' : '' ?>>Inclusive</option>
                            <option value="exclusive" <?= ($settings['vat_type'] ?? '') === 'exclusive' ? 'selected' : '' ?>>Exclusive</option>
                        </select>
                    </div>

                    <!-- Share Click Deduplication Window -->
                    <div class="form-group">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">Share Link Click Tracking Deduplication Window</label>
                        <p style="font-size: 12px; color: #718096; margin-bottom: 8px;">
                            Repeat clicks from the same user/IP within this duration will count as 1 click.
                        </p>
                        <select name="share_click_dedup_minutes" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                            <?php 
                                $currentDedup = (int)($settings['share_click_dedup_minutes'] ?? 60);
                                $dedupOptions = [
                                    15 => '15 Minutes',
                                    30 => '30 Minutes',
                                    60 => '1 Hour (Default)',
                                    90 => '1 Hour 30 Minutes',
                                    120 => '2 Hours',
                                    360 => '6 Hours',
                                    720 => '12 Hours',
                                    1440 => '24 Hours (1 Day)'
                                ];
                                foreach ($dedupOptions as $mins => $label) {
                                    $sel = ($mins === $currentDedup) ? 'selected' : '';
                                    echo "<option value=\"$mins\" $sel>$label</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: CURRENCY EXCHANGE RATES -->
        <div id="currencyTab" class="settings-pane">
            <div style="background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; width: 100%; box-sizing: border-box;">
                <h2 style="font-size: 18px; margin-top: 0; margin-bottom: 12px; color: #2b6cb0;">
                    💱 Multi-Currency Exchange Rates Configuration (Relative to 1.000 BHD)
                </h2>
                <p style="font-size: 13px; color: #718096; margin-bottom: 24px;">
                    Configure exchange rates used for storefront multi-currency price conversions and payment gateway checkout calculations. (Example: 1 BHD = 9.95 SAR).
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                    <!-- KWD -->
                    <div class="form-group" style="background: #f7fafc; padding: 14px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">🇰🇼 Kuwaiti Dinar (KWD)</label>
                        <input type="number" step="0.01" min="0.01" name="currency_rate_kwd" value="<?= htmlspecialchars($settings['currency_rate_kwd'] ?? '0.81') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    </div>

                    <!-- SAR -->
                    <div class="form-group" style="background: #f7fafc; padding: 14px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">🇸🇦 Saudi Riyal (SAR)</label>
                        <input type="number" step="0.01" min="0.01" name="currency_rate_sar" value="<?= htmlspecialchars($settings['currency_rate_sar'] ?? '9.95') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    </div>

                    <!-- AED -->
                    <div class="form-group" style="background: #f7fafc; padding: 14px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">🇦🇪 UAE Dirham (AED)</label>
                        <input type="number" step="0.01" min="0.01" name="currency_rate_aed" value="<?= htmlspecialchars($settings['currency_rate_aed'] ?? '9.76') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    </div>

                    <!-- QAR -->
                    <div class="form-group" style="background: #f7fafc; padding: 14px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">🇶🇦 Qatari Riyal (QAR)</label>
                        <input type="number" step="0.01" min="0.01" name="currency_rate_qar" value="<?= htmlspecialchars($settings['currency_rate_qar'] ?? '9.67') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    </div>

                    <!-- OMR -->
                    <div class="form-group" style="background: #f7fafc; padding: 14px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">🇴🇲 Omani Rial (OMR)</label>
                        <input type="number" step="0.01" min="0.01" name="currency_rate_omr" value="<?= htmlspecialchars($settings['currency_rate_omr'] ?? '1.02') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    </div>

                    <!-- USD -->
                    <div class="form-group" style="background: #f7fafc; padding: 14px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">🇺🇸 US Dollar (USD)</label>
                        <input type="number" step="0.01" min="0.01" name="currency_rate_usd" value="<?= htmlspecialchars($settings['currency_rate_usd'] ?? '2.65') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    </div>

                    <!-- EUR -->
                    <div class="form-group" style="background: #f7fafc; padding: 14px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">🇪🇺 Euro (EUR)</label>
                        <input type="number" step="0.01" min="0.01" name="currency_rate_eur" value="<?= htmlspecialchars($settings['currency_rate_eur'] ?? '2.44') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: SIZE GUIDE CONFIGURATION -->
        <div id="sizeguideTab" class="settings-pane">
            <div style="background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; width: 100%; box-sizing: border-box;">
                <h2 style="font-size: 18px; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px; color: #2d3748;">
                    📐 Size Guide Configuration &amp; Measurement Charts
                </h2>
                
                <!-- Descriptions -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 6px; font-size: 13px;">English Description</label>
                        <textarea name="size_guide_desc_en" rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;"><?= htmlspecialchars($settings['size_guide_desc_en'] ?? 'This chart shows the recommended length based on height. Please double-check with your own measurement to be sure of your perfect fit.') ?></textarea>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 6px; font-size: 13px;">Arabic Description</label>
                        <textarea name="size_guide_desc_ar" rows="2" dir="rtl" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;"><?= htmlspecialchars($settings['size_guide_desc_ar'] ?? 'هذا الجدول يوضح الطول الموصى به حسب طولك. يُرجى التأكد بالمتر لقياسك الشخصي للحصول على المقاس الأنسب لكِ.') ?></textarea>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <!-- Chest & Shoulder Guide Table -->
                    <div style="background: #f7fafc; padding: 18px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <h4 style="margin-top: 0; margin-bottom: 14px; font-size: 15px; color: #2d3748;">Chest &amp; Shoulder Chart</h4>
                        <table id="chest_guide_table" style="width: 100%; border-collapse: collapse; margin-bottom: 12px; background: #fff;">
                            <thead>
                                <tr style="background: #edf2f7;">
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left; font-size: 12px;">Size</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left; font-size: 12px;">Chest (Inch)</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left; font-size: 12px;">Shoulder (Inch)</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; width: 50px; font-size: 12px;">Act</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS Populated -->
                            </tbody>
                        </table>
                        <button type="button" onclick="addChestRow()" style="padding: 6px 12px; background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">+ Add Row</button>
                        <input type="hidden" name="size_guide_chest" id="size_guide_chest_input">
                    </div>

                    <!-- Abaya Length by Height Table -->
                    <div style="background: #f7fafc; padding: 18px; border-radius: 6px; border: 1px solid #edf2f7;">
                        <h4 style="margin-top: 0; margin-bottom: 14px; font-size: 15px; color: #2d3748;">Abaya Length by Height Chart</h4>
                        <table id="length_guide_table" style="width: 100%; border-collapse: collapse; margin-bottom: 12px; background: #fff;">
                            <thead>
                                <tr style="background: #edf2f7;">
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left; font-size: 12px;">Length (Inch)</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left; font-size: 12px;">Height (CM)</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; width: 50px; font-size: 12px;">Act</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS Populated -->
                            </tbody>
                        </table>
                        <button type="button" onclick="addLengthRow()" style="padding: 6px 12px; background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">+ Add Row</button>
                        <input type="hidden" name="size_guide_length" id="size_guide_length_input">
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: PAYMENT GATEWAY CONFIG -->
        <div id="paymentTab" class="settings-pane">
            <div style="background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; width: 100%; box-sizing: border-box;">
                <h2 style="font-size: 18px; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px; color: #2d3748;">
                    💳 Payment Gateway Configuration (AFS / OPPWA)
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 24px;">
                    <div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 700; color: #2d3748; font-size: 14px;">
                                <input type="checkbox" name="afs_gateway_enabled" value="1" <?= ($settings['afs_gateway_enabled'] ?? '0') === '1' ? 'checked' : '' ?> style="margin-right: 10px; width: 18px; height: 18px;">
                                Enable AFS Payment Gateway on Checkout
                            </label>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">Gateway Display Name</label>
                            <input type="text" name="afs_gateway_name" value="<?= htmlspecialchars($settings['afs_gateway_name'] ?? 'AFS Invoicing Gateway') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">API Base URL / Endpoint</label>
                            <input type="url" name="afs_api_endpoint" value="<?= htmlspecialchars($settings['afs_api_endpoint'] ?? 'https://test.oppwa.com') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                            <p style="font-size: 12px; color: #718096; margin-top: 6px;">Use <code>https://test.oppwa.com</code> for testing and <code>https://oppwa.com</code> for live production.</p>
                        </div>
                    </div>

                    <div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">Entity ID</label>
                            <input type="text" name="afs_entity_id" value="<?= htmlspecialchars($settings['afs_entity_id'] ?? '') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">Access Token (Bearer Auth)</label>
                            <input type="text" name="afs_access_token" value="<?= htmlspecialchars($settings['afs_access_token'] ?? '') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">Payment Gateway Settlement Currency Code</label>
                            <select name="afs_currency" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                                <?php
                                $gwCurrencies = [
                                    'BHD' => '🇧🇭 BHD - Bahraini Dinar',
                                    'SAR' => '🇸🇦 SAR - Saudi Riyal',
                                    'KWD' => '🇰🇼 KWD - Kuwaiti Dinar',
                                    'AED' => '🇦🇪 AED - UAE Dirham',
                                    'QAR' => '🇶🇦 QAR - Qatari Riyal',
                                    'OMR' => '🇴🇲 OMR - Omani Rial',
                                    'USD' => '🇺🇸 USD - US Dollar',
                                    'EUR' => '🇪🇺 EUR - Euro'
                                ];
                                $currentGwCurrency = strtoupper($settings['afs_currency'] ?? 'BHD');
                                foreach ($gwCurrencies as $cCode => $cLabel) {
                                    $sel = ($cCode === $currentGwCurrency) ? 'selected' : '';
                                    echo "<option value=\"$cCode\" $sel>$cLabel</option>";
                                }
                                ?>
                            </select>
                            <p style="font-size: 12px; color: #718096; margin-top: 6px;">Select the exact currency code authorized for your AFS Payment Gateway merchant account.</p>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">Gateway Charge Currency Mode</label>
                            <select name="afs_charge_currency_mode" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                                <option value="base" <?= ($settings['afs_charge_currency_mode'] ?? 'base') === 'base' ? 'selected' : '' ?>>
                                    Always Charge in Store Base Currency (e.g. BHD) — Recommended
                                </option>
                                <option value="customer_currency" <?= ($settings['afs_charge_currency_mode'] ?? 'base') === 'customer_currency' ? 'selected' : '' ?>>
                                    Charge in Customer Selected Currency (Requires Multi-Currency MID)
                                </option>
                            </select>
                            <p style="font-size: 12px; color: #718096; margin-top: 6px;">
                                Select <strong>Base Currency (BHD)</strong> if your AFS account is single-currency. Customers view prices converted in SAR/KWD, and payment checkout processes seamlessly in base BHD.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: POLICIES & LEGAL TERMS -->
        <div id="policiesTab" class="settings-pane">
            <div style="background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; width: 100%; box-sizing: border-box;">
                <h2 style="font-size: 18px; margin-top: 0; margin-bottom: 12px; color: #2b6cb0;">
                    📜 Store Policies &amp; Legal Terms Configuration
                </h2>
                <p style="color: #718096; font-size: 13px; margin-bottom: 24px;">
                    Use the Summernote WYSIWYG text editors below to format customer policies visually.
                </p>

                <!-- 1. Shipping & GCC Delivery -->
                <div style="margin-bottom: 28px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 8px; font-size: 14px;">
                        🚚 Shipping &amp; GCC Delivery Policy (`shipping_policy`)
                    </label>
                    <textarea name="shipping_policy" id="shipping_policy" class="policy-editor" rows="8" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;"><?= htmlspecialchars($settings['shipping_policy'] ?? '<h3>Shipping & GCC Delivery Policy</h3><p>At Dar Jana Fashion, we craft and deliver high-couture luxury abayas and sets across Bahrain and all GCC countries.</p>') ?></textarea>
                </div>

                <!-- 2. Returns & Exchanges -->
                <div style="margin-bottom: 28px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 8px; font-size: 14px;">
                        🔄 Returns &amp; Exchanges Policy (`return_policy`)
                    </label>
                    <textarea name="return_policy" id="return_policy" class="policy-editor" rows="8" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;"><?= htmlspecialchars($settings['return_policy'] ?? '<h3>Returns & Exchanges Policy</h3><p>We take pride in our craftsmanship. Returns and exchanges are accepted within 7 days of delivery.</p>') ?></textarea>
                </div>

                <!-- 3. Terms & Conditions -->
                <div style="margin-bottom: 28px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 8px; font-size: 14px;">
                        📜 Terms &amp; Conditions (`terms_conditions`)
                    </label>
                    <textarea name="terms_conditions" id="terms_conditions" class="policy-editor" rows="8" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;"><?= htmlspecialchars($settings['terms_conditions'] ?? '<h3>Terms & Conditions</h3><p>Welcome to Dar Jana Fashion. By accessing our site, you agree to our terms.</p>') ?></textarea>
                </div>

                <!-- 4. Privacy Policy -->
                <div style="margin-bottom: 28px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 8px; font-size: 14px;">
                        🔒 Privacy Policy (`privacy_policy`)
                    </label>
                    <textarea name="privacy_policy" id="privacy_policy" class="policy-editor" rows="8" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;"><?= htmlspecialchars($settings['privacy_policy'] ?? '<h3>Privacy Policy</h3><p>Dar Jana Fashion values your privacy and secures customer information.</p>') ?></textarea>
                </div>
            </div>
        </div>

        <!-- TAB 5: WEBSITE HEADER & BANNERS -->
        <div id="headerTab" class="settings-pane">
            <div style="background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; width: 100%; box-sizing: border-box;">
                <h2 style="font-size: 18px; margin-top: 0; margin-bottom: 12px; color: #2b6cb0;">
                    📢 Website Header Banner &amp; Homepage Promotions
                </h2>
                <p style="color: #718096; font-size: 13px; margin-bottom: 24px;">
                    Customize the top announcement ticker banner text and the homepage promotional callout block.
                </p>

                <!-- Header Background Colors -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                            🖥️ Web View Header Color
                        </label>
                        <input type="color" name="header_bg_web" value="<?= htmlspecialchars($settings['header_bg_web'] ?? '#ffffff') ?>" class="form-control" style="width: 100%; height: 40px; padding: 2px; border: 1px solid #cbd5e0; border-radius: 4px; cursor: pointer;">
                        <span style="font-size: 12px; color: #718096;">Background color for the header on desktop devices.</span>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                            📱 Mobile View Header Color
                        </label>
                        <input type="color" name="header_bg_mobile" value="<?= htmlspecialchars($settings['header_bg_mobile'] ?? '#ffffff') ?>" class="form-control" style="width: 100%; height: 40px; padding: 2px; border: 1px solid #cbd5e0; border-radius: 4px; cursor: pointer;">
                        <span style="font-size: 12px; color: #718096;">Background color for the header on mobile/tablet devices.</span>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                            🖥️ Web View Text/Icon Color
                        </label>
                        <input type="color" name="header_text_web" value="<?= htmlspecialchars($settings['header_text_web'] ?? '#000000') ?>" class="form-control" style="width: 100%; height: 40px; padding: 2px; border: 1px solid #cbd5e0; border-radius: 4px; cursor: pointer;">
                        <span style="font-size: 12px; color: #718096;">Color of the navigation links and icons on desktop.</span>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                            📱 Mobile View Text/Icon Color
                        </label>
                        <input type="color" name="header_text_mobile" value="<?= htmlspecialchars($settings['header_text_mobile'] ?? '#000000') ?>" class="form-control" style="width: 100%; height: 40px; padding: 2px; border: 1px solid #cbd5e0; border-radius: 4px; cursor: pointer;">
                        <span style="font-size: 12px; color: #718096;">Color of the navigation links and icons on mobile.</span>
                    </div>
                </div>

                <!-- 1. Top Announcement Bar -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                        📣 Top Announcement Bar Text (Header Ticker)
                    </label>
                    <input type="text" name="top_announcement_bar" value="<?= htmlspecialchars($settings['top_announcement_bar'] ?? 'EXPRESS GCC DELIVERY TO BAHRAIN, KUWAIT, KSA, UAE, QATAR & OMAN') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                    <span style="font-size: 12px; color: #718096;">Displays at the very top of all pages.</span>
                </div>

                <!-- 2. Promotion Tagline -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                        🏷️ Homepage Promotion Tagline
                    </label>
                    <input type="text" name="promo_tagline" value="<?= htmlspecialchars($settings['promo_tagline'] ?? 'PROMOTION') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                </div>

                <!-- 3. Promotion Title -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                        📢 Homepage Promotion Heading / Title
                    </label>
                    <input type="text" name="promo_title" value="<?= htmlspecialchars($settings['promo_title'] ?? 'التوصيل مجاني لمدة أسبوع') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 14px;">
                </div>

                <!-- 4. Promotion Description -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                        📝 Homepage Promotion Description
                    </label>
                    <textarea name="promo_desc" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px; line-height: 1.5;"><?= htmlspecialchars($settings['promo_desc'] ?? 'Enjoy complimentary express delivery on all dress and abaya orders across all GCC regions for a limited period.') ?></textarea>
                </div>

                <hr style="margin: 32px 0 24px; border: 0; border-top: 1px dashed #cbd5e0;">

                <!-- 5. Homepage Hero Video Upload -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                        🎥 Homepage Hero Video (`home_hero_video`)
                    </label>
                    <p style="font-size: 12px; color: #718096; margin-bottom: 12px;">
                        Upload a video for the main hero banner on the homepage. Maximum allowed size is <strong>50 MB</strong>. Supported formats: MP4, WebM, OGG, MOV. <em>Uploading a new video will delete the existing video file.</em>
                    </p>

                    <?php if (!empty($settings['home_hero_video'])): ?>
                        <div style="background: #f7fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 16px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                            <video src="<?= BASE_URL . htmlspecialchars($settings['home_hero_video']) ?>" controls style="max-width: 320px; max-height: 180px; border-radius: 6px; background: #000;"></video>
                            <div>
                                <div style="font-weight: 600; color: #2d3748; font-size: 13px; margin-bottom: 4px;">Current Active Video:</div>
                                <code style="font-size: 12px; color: #4a5568; background: #edf2f7; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-bottom: 8px; word-break: break-all;">
                                    <?= htmlspecialchars($settings['home_hero_video']) ?>
                                </code>
                                <div style="margin-top: 6px;">
                                    <label style="display: inline-flex; align-items: center; gap: 6px; color: #e53e3e; font-size: 13px; cursor: pointer; font-weight: 600;">
                                        <input type="checkbox" name="delete_home_hero_video" value="1"> 🗑️ Delete current video &amp; revert to default
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="background: #fffaf0; border: 1px solid #feebc8; color: #c05621; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px;">
                            ℹ️ No custom video uploaded. The default fallback video (<code>/assets/videos/home_video.mp4</code>) is currently active on the homepage.
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                        <input type="file" name="home_hero_video" id="homeHeroVideoInput" accept="video/mp4,video/webm,video/ogg,video/quicktime" onchange="validateHeroVideoSize(this)" style="padding: 10px; border: 1px dashed #cbd5e0; border-radius: 6px; background: #fff; max-width: 450px; width: 100%;">
                    </div>
                    <div id="heroVideoError" style="color: #e53e3e; font-size: 13px; font-weight: 600; margin-top: 8px; display: none;"></div>
                </div>
            </div>
        </div>

        <!-- SAVE SETTINGS BAR -->
        <div style="margin-top: 30px; border-top: 2px solid #e2e8f0; padding-top: 20px;">
            <button type="submit" class="btn-primary" style="padding: 14px 32px; border-radius: 6px; background: #181818; color: #fff; border: none; cursor: pointer; font-weight: 700; font-size: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                💾 Save All Settings
            </button>
        </div>
    </form>
</div>

<!-- jQuery, DataTables & Summernote WYSIWYG Text Editor -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #cbd5e0; padding: 4px 8px; border-radius: 4px; margin-left: 8px; }
    .dataTables_wrapper .dataTables_length select { border: 1px solid #cbd5e0; padding: 4px; border-radius: 4px; }
    .note-editor .note-toolbar { background: #f7fafc; border-bottom: 1px solid #e2e8f0; }
    .note-editor.note-frame { border: 1px solid #cbd5e0; border-radius: 6px; overflow: hidden; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
function switchSettingsTab(tabId, btn) {
    document.querySelectorAll('.settings-pane').forEach(function(el) {
        el.classList.remove('active');
    });
    document.querySelectorAll('.settings-tab-btn').forEach(function(el) {
        el.classList.remove('active');
    });

    const targetPane = document.getElementById(tabId);
    if (targetPane) {
        targetPane.classList.add('active');
    }
    if (btn) {
        btn.classList.add('active');
    }
}

$(document).ready(function() {
    // Summernote WYSIWYG Initialization for Policy Editors
    $('.policy-editor').summernote({
        height: 220,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        placeholder: 'Enter policy terms and conditions here...'
    });
});

const defaultChest = [
    {size: 'S', chest: '20.00', shoulder: '27.00'},
    {size: 'M', chest: '23.00', shoulder: '28.00'},
    {size: 'L', chest: '24.00', shoulder: '29.00'},
    {size: 'XL', chest: '25.00', shoulder: '29.50'},
    {size: 'XXL', chest: '26.00', shoulder: '30.50'}
];
const defaultLength = [
    {length: '49.00', height: '150'},
    {length: '50.00', height: '151'},
    {length: '50.00', height: '152'},
    {length: '51.00', height: '153'},
    {length: '51.00', height: '154'},
    {length: '52.00', height: '155'},
    {length: '52.00', height: '156'}
];

let chestData = <?= !empty($settings['size_guide_chest']) ? $settings['size_guide_chest'] : 'JSON.stringify(defaultChest)' ?>;
let lengthData = <?= !empty($settings['size_guide_length']) ? $settings['size_guide_length'] : 'JSON.stringify(defaultLength)' ?>;

if (typeof chestData === 'string') {
    try { chestData = JSON.parse(chestData); } catch(e) { chestData = defaultChest; }
}
if (typeof lengthData === 'string') {
    try { lengthData = JSON.parse(lengthData); } catch(e) { lengthData = defaultLength; }
}

function renderChestTable() {
    const tbody = document.querySelector('#chest_guide_table tbody');
    tbody.innerHTML = '';
    chestData.forEach((row, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 6px; border: 1px solid #e2e8f0;"><input type="text" value="${row.size || ''}" onchange="updateChest(${index}, 'size', this.value)" style="width: 100%; border: none; padding: 4px;"></td>
            <td style="padding: 6px; border: 1px solid #e2e8f0;"><input type="text" value="${row.chest || ''}" onchange="updateChest(${index}, 'chest', this.value)" style="width: 100%; border: none; padding: 4px;"></td>
            <td style="padding: 6px; border: 1px solid #e2e8f0;"><input type="text" value="${row.shoulder || ''}" onchange="updateChest(${index}, 'shoulder', this.value)" style="width: 100%; border: none; padding: 4px;"></td>
            <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: center;"><button type="button" onclick="removeChestRow(${index})" style="color: red; border: none; background: none; cursor: pointer;">✕</button></td>
        `;
        tbody.appendChild(tr);
    });
    document.getElementById('size_guide_chest_input').value = JSON.stringify(chestData);
}

function renderLengthTable() {
    const tbody = document.querySelector('#length_guide_table tbody');
    tbody.innerHTML = '';
    lengthData.forEach((row, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 6px; border: 1px solid #e2e8f0;"><input type="text" value="${row.length || ''}" onchange="updateLength(${index}, 'length', this.value)" style="width: 100%; border: none; padding: 4px;"></td>
            <td style="padding: 6px; border: 1px solid #e2e8f0;"><input type="text" value="${row.height || ''}" onchange="updateLength(${index}, 'height', this.value)" style="width: 100%; border: none; padding: 4px;"></td>
            <td style="padding: 6px; border: 1px solid #e2e8f0; text-align: center;"><button type="button" onclick="removeLengthRow(${index})" style="color: red; border: none; background: none; cursor: pointer;">✕</button></td>
        `;
        tbody.appendChild(tr);
    });
    document.getElementById('size_guide_length_input').value = JSON.stringify(lengthData);
}

function updateChest(index, field, val) {
    chestData[index][field] = val;
    document.getElementById('size_guide_chest_input').value = JSON.stringify(chestData);
}
function updateLength(index, field, val) {
    lengthData[index][field] = val;
    document.getElementById('size_guide_length_input').value = JSON.stringify(lengthData);
}
function addChestRow() {
    chestData.push({size: '', chest: '', shoulder: ''});
    renderChestTable();
}
function removeChestRow(index) {
    chestData.splice(index, 1);
    renderChestTable();
}
function addLengthRow() {
    lengthData.push({length: '', height: ''});
    renderLengthTable();
}
function removeLengthRow(index) {
    lengthData.splice(index, 1);
    renderLengthTable();
}

function validateHeroVideoSize(input) {
    const errorDiv = document.getElementById('heroVideoError');
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxSize = 50 * 1024 * 1024; // 50MB in bytes
        if (file.size > maxSize) {
            errorDiv.textContent = '❌ Selected video file is ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB. Maximum allowed size is 50 MB.';
            errorDiv.style.display = 'block';
            input.value = ''; // clear file input
        }
    }
}

renderChestTable();
renderLengthTable();

document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const videoInput = document.getElementById('homeHeroVideoInput');
    if (videoInput && videoInput.files && videoInput.files[0]) {
        const file = videoInput.files[0];
        if (file.size > 50 * 1024 * 1024) {
            alert('Video file size must not exceed 50 MB.');
            e.preventDefault();
            return false;
        }
    }
    document.getElementById('size_guide_chest_input').value = JSON.stringify(chestData);
    document.getElementById('size_guide_length_input').value = JSON.stringify(lengthData);
});
</script>
</body>
</html>
