<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div class="admin-header-row" style="margin-bottom: 30px;">
                <h1 style="font-size: 26px;">Store Settings</h1>
                <p style="color: #718096; font-size: 14px;">Manage tax and timezone configurations</p>
            </div>

            <?php if (isset($_SESSION['admin_success'])): ?>
                <div style="background-color: #f0fdf4; color: #16a34a; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
                    <?= htmlspecialchars($_SESSION['admin_success']) ?>
                    <?php unset($_SESSION['admin_success']); ?>
                </div>
            <?php endif; ?>

            <div class="admin-card" style="max-width: 1400px; width: 100%;">
                <form method="POST" action="<?= BASE_URL ?>/admin/settings">
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; align-items: start;">
<!-- COLUMN 1: GENERAL -->
<div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
<h2 style="font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">General Settings</h2>
                    
                    <div class="form-group" style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Timezone</label>
                        <p style="font-size: 13px; color: #666; margin-bottom: 8px;">Sets the default time for order creation.</p>
                        <select name="timezone" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
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

                    <div class="form-group" style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">VAT Percentage (%)</label>
                        <input type="number" name="vat_percentage" step="0.1" min="0" value="<?= htmlspecialchars($settings['vat_percentage'] ?? '5') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">VAT Calculation Type</label>
                        <p style="font-size: 13px; color: #666; margin-bottom: 8px;">
                            <strong>Inclusive:</strong> Prices already include VAT. The total will not change.<br>
                            <strong>Exclusive:</strong> VAT is added on top of the product price at checkout.<br>
                            <strong>Without VAT:</strong> No VAT will be calculated or displayed.
                        </p>
                        <select name="vat_type" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
                            <option value="none" <?= ($settings['vat_type'] ?? '') === 'none' ? 'selected' : '' ?>>Without VAT (None)</option>
                            <option value="inclusive" <?= ($settings['vat_type'] ?? '') === 'inclusive' ? 'selected' : '' ?>>Inclusive</option>
                            <option value="exclusive" <?= ($settings['vat_type'] ?? '') === 'exclusive' ? 'selected' : '' ?>>Exclusive</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Share Link Click Tracking Deduplication Window</label>
                        <p style="font-size: 13px; color: #666; margin-bottom: 8px;">
                            Repeat clicks from the same user/IP within this duration will be ignored (counted as 1 click). After this duration, a new click will be counted again.
                        </p>
                        <select name="share_click_dedup_minutes" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
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

<!-- COLUMN 2: CHEST & SHOULDER -->
<div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
<h2 style="font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Size Guide - Chest & Shoulder</h2>
                    
                    <!-- Chest & Shoulder Guide -->
                    <div class="form-group" style="margin-bottom: 24px;">
                        
                        <table id="chest_guide_table" style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left;">Size</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left;">Chest (Inch)</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left;">Shoulder (Inch)</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; width: 50px;">Act</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS will populate this -->
                            </tbody>
                        </table>
                        <button type="button" onclick="addChestRow()" style="padding: 6px 12px; background: #edf2f7; border: 1px solid #cbd5e0; border-radius: 4px; cursor: pointer; font-size: 13px;">+ Add Row</button>
                        <input type="hidden" name="size_guide_chest" id="size_guide_chest_input">
                    </div>

                    </div>

<!-- COLUMN 3: LENGTH & HEIGHT -->
<div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
<h2 style="font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Abaya Length by Height</h2>
<div class="form-group" style="margin-bottom: 30px;">
                        
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; color: #4a5568; margin-bottom: 4px;">English Description</label>
                            <textarea name="size_guide_desc_en" rows="2" style="width: 100%; padding: 8px; border: 1px solid var(--color-border); border-radius: 4px;"><?= htmlspecialchars($settings['size_guide_desc_en'] ?? 'This chart shows the recommended length based on height. Please double-check with your own measurement to be sure of your perfect fit.') ?></textarea>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; color: #4a5568; margin-bottom: 4px;">Arabic Description</label>
                            <textarea name="size_guide_desc_ar" rows="2" dir="rtl" style="width: 100%; padding: 8px; border: 1px solid var(--color-border); border-radius: 4px;"><?= htmlspecialchars($settings['size_guide_desc_ar'] ?? 'هذا الجدول يوضح الطول الموصى به حسب طولك. يُرجى التأكد بالمتر لقياسك الشخصي للحصول على المقاس الأنسب لكِ.') ?></textarea>
                        </div>

                        <table id="length_guide_table" style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left;">Abaya Length (Inch)</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: left;">Your Height (CM)</th>
                                    <th style="padding: 8px; border: 1px solid #e2e8f0; text-align: center; width: 50px;">Act</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS will populate this -->
                            </tbody>
                        </table>
                        <button type="button" onclick="addLengthRow()" style="padding: 6px 12px; background: #edf2f7; border: 1px solid #cbd5e0; border-radius: 4px; cursor: pointer; font-size: 13px;">+ Add Row</button>
                        <input type="hidden" name="size_guide_length" id="size_guide_length_input">
                    </div>

                    </div>
</div>

<!-- PAYMENT GATEWAY SETTINGS -->
<div style="margin-top: 30px; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
    <h2 style="font-size: 20px; margin-bottom: 20px;">Payment Gateway Configuration (AFS / OPPWA)</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; align-items: start;">
        
        <div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="afs_gateway_enabled" value="1" <?= ($settings['afs_gateway_enabled'] ?? '0') === '1' ? 'checked' : '' ?> style="margin-right: 10px; width: 18px; height: 18px;">
                    Enable AFS Payment Gateway on Checkout
                </label>
            </div>
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Gateway Name</label>
                <input type="text" name="afs_gateway_name" value="<?= htmlspecialchars($settings['afs_gateway_name'] ?? 'AFS Invoicing Gateway') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">API Base URL / Endpoint</label>
                <input type="url" name="afs_api_endpoint" value="<?= htmlspecialchars($settings['afs_api_endpoint'] ?? 'https://test.oppwa.com') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
                <p style="font-size: 13px; color: #666; margin-top: 6px;">Use <code>https://test.oppwa.com</code> for testing and <code>https://oppwa.com</code> for live production.</p>
            </div>
        </div>

        <div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Entity ID</label>
                <input type="text" name="afs_entity_id" value="<?= htmlspecialchars($settings['afs_entity_id'] ?? '') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Access Token (Bearer Auth)</label>
                <input type="text" name="afs_access_token" value="<?= htmlspecialchars($settings['afs_access_token'] ?? '') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Default Currency</label>
                <input type="text" name="afs_currency" value="<?= htmlspecialchars($settings['afs_currency'] ?? 'BHD') ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px;">
            </div>
        </div>

        <!-- Store Policies & Terms Configuration -->
        <div class="settings-card" style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); grid-column: span 2; margin-top: 24px;">
            <h3 style="font-size: 18px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px; margin-bottom: 20px; color: #2b6cb0;">
                📜 Store Policies & Legal Terms Configuration
            </h3>
            <p style="color: #718096; font-size: 13px; margin-bottom: 20px;">
                Admin can enter formatted content, HTML tags, bullet lists, or rich terms for customer policies. These populate the customer policy pages and footer links.
            </p>

            <!-- 1. Shipping & GCC Delivery -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                    🚚 Shipping &amp; GCC Delivery Policy (`shipping_policy`)
                </label>
                <textarea name="shipping_policy" id="shipping_policy" class="policy-editor" rows="8" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;"><?= htmlspecialchars($settings['shipping_policy'] ?? '<h3>Shipping & GCC Delivery Policy</h3>
<p>At Dar Jana Fashion, we craft and deliver high-couture luxury abayas and sets across Bahrain and all GCC countries (Saudi Arabia, Kuwait, United Arab Emirates, Qatar, Oman) as well as international destinations.</p>

<h4>1. Order Processing & Tailoring Time</h4>
<p>Every garment is hand-crafted with precision. Standard tailoring and quality inspection take <strong>3 to 5 business days</strong> prior to dispatch.</p>

<h4>2. Shipping Times & Carriers</h4>
<ul>
  <li><strong>Bahrain Local Express:</strong> 1 to 2 business days (1.500 BHD or Free on orders above 50 BHD).</li>
  <li><strong>GCC Regional Shipping (KSA, UAE, Kuwait, Qatar, Oman):</strong> 3 to 5 business days via DHL / Aramex Express.</li>
  <li><strong>Worldwide Express Shipping:</strong> 5 to 7 business days.</li>
</ul>

<h4>3. Tracking & Receipts</h4>
<p>Once dispatched, customers receive an SMS and Email containing an instant tracking link and official courier waybill receipt.</p>') ?></textarea>
            </div>

            <!-- 2. Returns & Exchanges -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                    🔄 Returns &amp; Exchanges Policy (`return_policy`)
                </label>
                <textarea name="return_policy" id="return_policy" class="policy-editor" rows="8" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;"><?= htmlspecialchars($settings['return_policy'] ?? '<h3>Returns & Exchanges Policy</h3>
<p>We take pride in the craftsmanship of Dar Jana Fashion. If you are not entirely satisfied with your item, we are here to assist you.</p>

<h4>1. Return Window</h4>
<p>You may request a return or exchange within <strong>7 days</strong> of receiving your shipment.</p>

<h4>2. Conditions for Returns</h4>
<ul>
  <li>Garments must be unworn, unwashed, unaltered, and free of perfume or stains.</li>
  <li>Original tags, luxury dust bags, and labels must remain attached.</li>
  <li>Customized measurements or made-to-order bespoke pieces are non-refundable unless defective.</li>
</ul>

<h4>3. Exchange Process</h4>
<p>To initiate an exchange, please contact our Concierge Customer Support via WhatsApp at <strong>+973 3330 0160</strong> or email <strong>care@darjanafashion.com</strong>.</p>') ?></textarea>
            </div>

            <!-- 3. Terms & Conditions -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                    📜 Terms &amp; Conditions (`terms_conditions`)
                </label>
                <textarea name="terms_conditions" id="terms_conditions" class="policy-editor" rows="8" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;"><?= htmlspecialchars($settings['terms_conditions'] ?? '<h3>Terms & Conditions</h3>
<p>Welcome to Dar Jana Fashion. By accessing our website, browsing our collections, or making a purchase, you agree to comply with and be bound by the following terms.</p>

<h4>1. Intellectual Property</h4>
<p>All designs, imagery, brand names, product codes, and content on this site are the exclusive property of Dar Jana Fashion.</p>

<h4>2. Pricing & Currency</h4>
<p>Prices are listed in Bahraini Dinar (BHD) and multi-currency equivalents for GCC regions. We reserve the right to modify prices without prior notice.</p>

<h4>3. Orders & Acceptance</h4>
<p>Order placement constitutes an offer to purchase. We reserve the right to cancel orders in case of stock unavailability or pricing anomalies.</p>') ?></textarea>
            </div>

            <!-- 4. Privacy Policy -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; color: #2d3748; margin-bottom: 6px; font-size: 14px;">
                    🔒 Privacy Policy (`privacy_policy`)
                </label>
                <textarea name="privacy_policy" id="privacy_policy" class="policy-editor" rows="8" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;"><?= htmlspecialchars($settings['privacy_policy'] ?? '<h3>Privacy Policy</h3>
<p>Dar Jana Fashion values your privacy and is dedicated to protecting your personal information.</p>

<h4>1. Information Collection</h4>
<p>We collect essential personal information (name, address, email, mobile number, and payment details) strictly to process your orders and enhance your shopping experience.</p>

<h4>2. Data Security & Third Parties</h4>
<p>We use SSL encryption and secure payment gateways. We never sell or share your personal data with unauthorized third parties.</p>

<h4>3. Your Rights</h4>
<p>You have the right to inspect, correct, or request deletion of your personal account details at any time by contacting our support team.</p>') ?></textarea>
            </div>
        </div>

    </div>
</div>

<div style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
    <button type="submit" class="btn-primary" style="padding: 12px 24px; border-radius: 4px; background: #181818; color: #fff; border: none; cursor: pointer; font-weight: 600;">Save Settings</button>
</div>
                </form>
            </div>
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
$(document).ready(function() {
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

let chestData = <?= $settings['size_guide_chest'] ?? 'null' ?> || defaultChest;
let lengthData = <?= $settings['size_guide_length'] ?? 'null' ?> || defaultLength;

function renderChestTable() {
    const tbody = document.querySelector('#chest_guide_table tbody');
    tbody.innerHTML = '';
    chestData.forEach((row, i) => {
        tbody.innerHTML += `
            <tr>
                <td style="padding: 4px; border: 1px solid #e2e8f0;"><input type="text" value="${row.size}" onchange="updateChest(${i}, 'size', this.value)" style="width:100%; padding: 4px; border:none; outline:none;"></td>
                <td style="padding: 4px; border: 1px solid #e2e8f0;"><input type="text" value="${row.chest}" onchange="updateChest(${i}, 'chest', this.value)" style="width:100%; padding: 4px; border:none; outline:none;"></td>
                <td style="padding: 4px; border: 1px solid #e2e8f0;"><input type="text" value="${row.shoulder}" onchange="updateChest(${i}, 'shoulder', this.value)" style="width:100%; padding: 4px; border:none; outline:none;"></td>
                <td style="padding: 4px; border: 1px solid #e2e8f0; text-align:center;"><button type="button" onclick="removeChest(${i})" style="color:red; background:none; border:none; cursor:pointer;">&times;</button></td>
            </tr>
        `;
    });
    document.getElementById('size_guide_chest_input').value = JSON.stringify(chestData);
}

let lengthTable;

function renderLengthTable() {
    if (lengthTable) {
        lengthTable.clear().rows.add(lengthData).draw(false);
    }
    document.getElementById('size_guide_length_input').value = JSON.stringify(lengthData);
}

function updateChest(i, key, val) { chestData[i][key] = val; document.getElementById('size_guide_chest_input').value = JSON.stringify(chestData); }
function updateLength(i, key, val) { lengthData[i][key] = val; document.getElementById('size_guide_length_input').value = JSON.stringify(lengthData); }
function addChestRow() { chestData.push({size: '', chest: '', shoulder: ''}); renderChestTable(); }
function removeChest(i) { chestData.splice(i, 1); renderChestTable(); }
function addLengthRow() { lengthData.push({length: '', height: ''}); renderLengthTable(); }
function removeLength(i) { lengthData.splice(i, 1); renderLengthTable(); }

document.addEventListener('DOMContentLoaded', () => {
    renderChestTable();
    
    lengthTable = $('#length_guide_table').DataTable({
        data: lengthData,
        paging: true,
        searching: true,
        info: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        columns: [
            { 
                data: 'length',
                render: function(data, type, row, meta) {
                    return `<input type="text" value="${data}" onchange="updateLength(${meta.row}, 'length', this.value)" style="width:100%; padding: 4px; border:1px solid #e2e8f0; outline:none;">`;
                }
            },
            { 
                data: 'height',
                render: function(data, type, row, meta) {
                    return `<input type="text" value="${data}" onchange="updateLength(${meta.row}, 'height', this.value)" style="width:100%; padding: 4px; border:1px solid #e2e8f0; outline:none;">`;
                }
            },
            {
                data: null,
                render: function(data, type, row, meta) {
                    return `<button type="button" onclick="removeLength(${meta.row})" style="color:red; background:none; border:none; cursor:pointer; text-align:center;">&times;</button>`;
                },
                orderable: false,
                className: 'text-center'
            }
        ]
    });
    
    renderLengthTable();
});
</script>
</body>
</html>
