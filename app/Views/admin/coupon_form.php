<?php include __DIR__ . '/header.php'; ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    /* Styling for Customer DataTable and Form Layout */
    .coupon-editor-container {
        display: grid;
        grid-template-columns: <?= ($audienceType === 'targeted') ? 'minmax(420px, 480px) minmax(500px, 1fr)' : '1fr' ?>;
        gap: 24px;
        align-items: start;
        transition: all 0.3s ease;
    }
    @media (max-width: 1024px) {
        .coupon-editor-container {
            grid-template-columns: 1fr !important;
        }
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #cbd5e0;
        padding: 6px 12px;
        border-radius: 4px;
        margin-left: 8px;
        font-size: 13px;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #cbd5e0;
        padding: 6px;
        border-radius: 4px;
        font-size: 13px;
    }
    table.dataTable thead th {
        border-bottom: 2px solid #e2e8f0 !important;
        font-size: 12px;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 10px 8px !important;
        background: #f8fafc;
    }
    table.dataTable tbody td {
        padding: 10px 8px !important;
        border-bottom: 1px solid #edf2f7 !important;
        font-size: 13px;
        vertical-align: middle;
    }
    table.dataTable tr:hover {
        background-color: #f7fafc !important;
    }
    .customer-badge-added {
        background: #c6f6d5;
        color: #22543d;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-quick-add {
        background: #ebf8ff;
        color: #2b6cb0;
        border: 1px solid #bee3f8;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-quick-add:hover {
        background: #3182ce;
        color: #fff;
    }
</style>

<?php 
$isEditing = isset($isEdit) && $isEdit; 
$actionUrl = $isEditing ? BASE_URL . '/admin/coupons/update/' . $coupon['id'] : BASE_URL . '/admin/coupons/store';
$codeValue = $isEditing ? $coupon['code'] : ($generatedCode ?? '');
$discountType = $isEditing ? $coupon['discount_type'] : 'percentage';
$discountValue = $isEditing ? $coupon['discount_value'] : '';
$startDate = $isEditing ? $coupon['start_date'] : date('Y-m-d');
$endDate = $isEditing ? $coupon['end_date'] : date('Y-m-d', strtotime('+30 days'));
$usageLimit = $isEditing ? $coupon['usage_limit_per_user'] : 1;
$audienceType = $isEditing ? ($coupon['audience_type'] ?? 'all') : 'all';
$targetedCustomers = $isEditing ? ($coupon['targeted_customers'] ?? '') : '';
$customersList = $customers ?? [];
?>

<div class="admin-main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1a202c;"><?= $isEditing ? 'Edit Coupon' : 'Create Coupon' ?></h1>
            <p style="color: #718096; font-size: 14px; margin-top: 4px;"><?= $isEditing ? 'Modify offer code settings and customer targeting' : 'Generate a new discount code for all or targeted customer groups' ?></p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/admin/coupons" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #fff; border: 1px solid #cbd5e0; border-radius: 6px; color: #4a5568; text-decoration: none; font-weight: 500; font-size: 14px;">
                &larr; Back to Coupons
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div style="background-color: #fed7d7; border-left: 4px solid #e53e3e; color: #c53030; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px; font-size: 14px;">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="coupon-editor-container" id="coupon_editor_container">
        
        <!-- LEFT COLUMN: Coupon Setup Form -->
        <div style="background: #fff; padding: 26px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; width: 100%;">
            <form action="<?= $actionUrl ?>" method="POST" id="coupon_form">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #4a5568; text-transform: uppercase; letter-spacing: 0.5px;">Coupon Code *</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="code" value="<?= htmlspecialchars($codeValue) ?>" style="flex-grow: 1; padding: 10px 14px; border: 1px solid var(--color-border); border-radius: 4px; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-family: monospace;" required placeholder="e.g. VIP50">
                        <?php if (!$isEditing): ?>
                            <button type="button" onclick="generateCode()" style="padding: 10px 14px; background-color: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;">Generate</button>
                        <?php endif; ?>
                    </div>
                    <p style="font-size: 12px; color: #718096; margin-top: 4px;">6-digit alphanumeric coupon code</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #4a5568; text-transform: uppercase; letter-spacing: 0.5px;">Discount Type</label>
                        <select name="discount_type" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px; background: #fff; font-size: 14px;">
                            <option value="percentage" <?= $discountType === 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
                            <option value="fixed" <?= $discountType === 'fixed' ? 'selected' : '' ?>>Fixed Amount (BHD)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #4a5568; text-transform: uppercase; letter-spacing: 0.5px;">Discount Value *</label>
                        <input type="number" step="0.01" min="0.01" name="discount_value" value="<?= htmlspecialchars($discountValue) ?>" required style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px; font-size: 14px;" placeholder="e.g. 10.00">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #4a5568; text-transform: uppercase; letter-spacing: 0.5px;">Start Date *</label>
                        <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #4a5568; text-transform: uppercase; letter-spacing: 0.5px;">End Date *</label>
                        <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px; font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #4a5568; text-transform: uppercase; letter-spacing: 0.5px;">Usage Limit Per User</label>
                    <input type="number" name="usage_limit_per_user" value="<?= htmlspecialchars($usageLimit) ?>" min="1" required style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px; font-size: 14px;">
                    <p style="font-size: 12px; color: #718096; margin-top: 4px;">Maximum times a single customer (by email or phone number) can use this coupon.</p>
                </div>

                <!-- Audience Targeting Section -->
                <div style="margin-bottom: 24px; padding: 18px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 12px; color: #2d3748; text-transform: uppercase; letter-spacing: 0.5px;">Target Audience / Eligibility *</label>
                    
                    <div style="display: flex; gap: 20px; margin-bottom: 14px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: 500; color: #2d3748;">
                            <input type="radio" name="audience_type" value="all" <?= $audienceType === 'all' ? 'checked' : '' ?> onchange="toggleAudienceField(this.value)">
                            <span>🌐 All Customers (Public)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: 500; color: #2d3748;">
                            <input type="radio" name="audience_type" value="targeted" <?= $audienceType === 'targeted' ? 'checked' : '' ?> onchange="toggleAudienceField(this.value)">
                            <span style="font-weight: 600; color: #6b46c1;">🎯 Targeted Audience Only</span>
                        </label>
                    </div>

                    <div id="targeted_box" style="display: <?= $audienceType === 'targeted' ? 'block' : 'none' ?>; margin-top: 14px; border-top: 1px dashed #cbd5e0; padding-top: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label style="font-size: 13px; font-weight: 600; color: #4a5568;">Eligible Mobile Numbers & Emails:</label>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <span id="target_count_badge" style="font-size: 11px; background: #e9d8fd; color: #6b46c1; font-weight: 700; padding: 2px 8px; border-radius: 10px;">0 entries</span>
                                <button type="button" onclick="clearTargetedTextarea()" style="background: none; border: none; color: #e53e3e; font-size: 12px; cursor: pointer; text-decoration: underline; padding: 0;">Clear</button>
                            </div>
                        </div>
                        <textarea name="targeted_customers" id="targeted_customers" rows="6" oninput="updateTargetCount(); syncTableHighlight();" style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: 4px; font-family: monospace; font-size: 13px; line-height: 1.5;" placeholder="Select customers from the directory on the right, or enter mobile numbers / emails manually (separated by newlines or commas)..."><?= htmlspecialchars($targetedCustomers) ?></textarea>
                        <p style="font-size: 12px; color: #718096; margin-top: 6px; line-height: 1.4;">
                            💡 Tip: Pick customers directly from the <strong>Available Customers</strong> table on the right to automatically insert them.
                        </p>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 14px; font-size: 15px; font-weight: 600; border-radius: 6px;">
                    <?= $isEditing ? 'Update Coupon' : 'Create Coupon' ?>
                </button>
            </form>
        </div>

        <!-- RIGHT COLUMN: Available Customers Directory (DataTable) -->
        <div id="available_customers_panel" style="display: <?= $audienceType === 'targeted' ? 'block' : 'none' ?>; background: #fff; padding: 22px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; width: 100%;">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px;">
                <div>
                    <h3 style="font-size: 17px; font-weight: 700; color: #2d3748; margin: 0;">👥 Available Customers Directory</h3>
                    <p style="color: #718096; font-size: 13px; margin: 4px 0 0 0;">Select customers below to add them to this coupon's targeted list.</p>
                </div>
            </div>

            <!-- Multi-Select Action Bar -->
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; background: #f7fafc; padding: 10px 14px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 16px; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #4a5568; cursor: pointer;">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                        <span>Select All</span>
                    </label>
                    <span id="selected_count_text" style="font-size: 12px; color: #718096;">(0 selected)</span>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 12px; color: #4a5568; font-weight: 500;">Add:</span>
                    <select id="insert_mode_select" style="padding: 4px 8px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 12px; background: #fff;">
                        <option value="both">Phone & Email</option>
                        <option value="phone">Phone Only</option>
                        <option value="email">Email Only</option>
                    </select>
                    <button type="button" onclick="addSelectedToAudience()" class="btn-primary" style="padding: 6px 14px; font-size: 12px; font-weight: 600; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                        ➕ Add Selected (<span id="btn_selected_count">0</span>)
                    </button>
                </div>
            </div>

            <!-- DataTable for Customers -->
            <div class="table-responsive">
                <table id="customersTable" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 30px; text-align: center;"></th>
                            <th>Customer</th>
                            <th>Phone / WhatsApp</th>
                            <th>Email</th>
                            <th style="text-align: center;">Orders</th>
                            <th style="text-align: right; width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($customersList)): ?>
                            <?php foreach ($customersList as $index => $c): ?>
                                <?php 
                                $phoneClean = trim($c['phone'] ?? '');
                                $emailClean = trim($c['email'] ?? '');
                                $nameClean = trim($c['customer_name'] ?? 'Customer');
                                $orderCount = (int)($c['total_orders'] ?? 0);
                                $totalSpent = (float)($c['total_spent'] ?? 0);
                                ?>
                                <tr id="cust_row_<?= $index ?>" data-phone="<?= htmlspecialchars($phoneClean) ?>" data-email="<?= htmlspecialchars($emailClean) ?>">
                                    <td style="text-align: center;">
                                        <input type="checkbox" class="customer-row-cb" value="<?= $index ?>" data-phone="<?= htmlspecialchars($phoneClean) ?>" data-email="<?= htmlspecialchars($emailClean) ?>" onchange="updateSelectedCount()">
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #2d3748;"><?= htmlspecialchars($nameClean) ?></div>
                                        <?php if (!empty($c['city']) && $c['city'] !== '-'): ?>
                                            <div style="font-size: 11px; color: #a0aec0;"><?= htmlspecialchars($c['city']) ?><?= (!empty($c['country']) && $c['country'] !== '-') ? ', ' . htmlspecialchars($c['country']) : '' ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($phoneClean)): ?>
                                            <span style="font-family: monospace; font-weight: 500; color: #2c5282;"><?= htmlspecialchars($phoneClean) ?></span>
                                        <?php else: ?>
                                            <span style="color: #cbd5e0; font-size: 11px;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($emailClean)): ?>
                                            <span style="color: #4a5568; font-size: 12px;"><?= htmlspecialchars($emailClean) ?></span>
                                        <?php else: ?>
                                            <span style="color: #cbd5e0; font-size: 11px;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if ($orderCount > 0): ?>
                                            <span style="background: #edf2f7; color: #4a5568; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px;">
                                                <?= $orderCount ?> (<?= number_format($totalSpent, 2) ?> BHD)
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #a0aec0; font-size: 11px;">Subscriber</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <button type="button" class="btn-quick-add" id="add_btn_<?= $index ?>" onclick="addSingleCustomer(<?= $index ?>, '<?= htmlspecialchars(addslashes($phoneClean)) ?>', '<?= htmlspecialchars(addslashes($emailClean)) ?>')">
                                            + Add
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<!-- jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
let dtInstance = null;

$(document).ready(function() {
    initCustomersTable();
    updateTargetCount();
    syncTableHighlight();
});

function initCustomersTable() {
    if ($('#customersTable').length && !$.fn.DataTable.isDataTable('#customersTable')) {
        dtInstance = $('#customersTable').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "",
                searchPlaceholder: "Search by name, phone, email...",
                lengthMenu: "Show _MENU_ customers",
                info: "Showing _START_ to _END_ of _TOTAL_ customers",
                emptyTable: "No customers found in database."
            },
            columnDefs: [
                { orderable: false, targets: [0, 5] } // Disable sorting on checkbox and action buttons
            ],
            order: [[4, 'desc']] // Default sort by Orders
        });

        // Re-sync row statuses on page change / search
        dtInstance.on('draw', function() {
            syncTableHighlight();
            updateSelectedCount();
        });
    }
}

function generateCode() {
    const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    let result = '';
    for (let i = 0; i < 6; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.querySelector('input[name="code"]').value = result;
}

function toggleAudienceField(val) {
    const box = document.getElementById('targeted_box');
    const panel = document.getElementById('available_customers_panel');
    const container = document.getElementById('coupon_editor_container');

    if (val === 'targeted') {
        box.style.display = 'block';
        panel.style.display = 'block';
        if (window.innerWidth > 1024) {
            container.style.gridTemplateColumns = 'minmax(420px, 480px) minmax(500px, 1fr)';
        }
        if (dtInstance) {
            dtInstance.columns.adjust().draw();
        } else {
            initCustomersTable();
        }
        syncTableHighlight();
    } else {
        box.style.display = 'none';
        panel.style.display = 'none';
        container.style.gridTemplateColumns = '1fr';
    }
}

function getTargetedEntries() {
    const val = document.getElementById('targeted_customers').value;
    return val.split(/[,\r\n;]+/).map(s => s.trim()).filter(s => s.length > 0);
}

function updateTargetCount() {
    const entries = getTargetedEntries();
    const countBadge = document.getElementById('target_count_badge');
    if (countBadge) {
        countBadge.innerText = entries.length + (entries.length === 1 ? ' entry' : ' entries');
    }
}

function clearTargetedTextarea() {
    if (confirm('Clear all targeted customer entries?')) {
        document.getElementById('targeted_customers').value = '';
        updateTargetCount();
        syncTableHighlight();
    }
}

function isCustomerInTargetList(phone, email) {
    const entries = getTargetedEntries();
    if (!entries.length) return false;

    const cleanPhoneDigits = (phone || '').replace(/\D/g, '');
    const cleanEmail = (email || '').toLowerCase().trim();

    for (let item of entries) {
        const itemLower = item.toLowerCase().trim();
        if (cleanEmail && itemLower === cleanEmail) return true;

        const itemDigits = item.replace(/\D/g, '');
        if (cleanPhoneDigits && itemDigits) {
            if (itemDigits === cleanPhoneDigits) return true;
            if (cleanPhoneDigits.length >= 8 && itemDigits.endsWith(cleanPhoneDigits)) return true;
            if (itemDigits.length >= 8 && cleanPhoneDigits.endsWith(itemDigits)) return true;
        }
    }
    return false;
}

function syncTableHighlight() {
    const rows = document.querySelectorAll('#customersTable tbody tr');
    rows.forEach(row => {
        const phone = row.getAttribute('data-phone');
        const email = row.getAttribute('data-email');
        const btn = row.querySelector('.btn-quick-add');
        if (!btn) return;

        if (isCustomerInTargetList(phone, email)) {
            btn.innerHTML = '✓ Added';
            btn.style.background = '#c6f6d5';
            btn.style.color = '#22543d';
            btn.style.borderColor = '#9ae6b4';
        } else {
            btn.innerHTML = '+ Add';
            btn.style.background = '#ebf8ff';
            btn.style.color = '#2b6cb0';
            btn.style.borderColor = '#bee3f8';
        }
    });
}

function addSingleCustomer(idx, phone, email) {
    const mode = document.getElementById('insert_mode_select').value;
    let toAdd = [];

    if (mode === 'both') {
        if (phone) toAdd.push(phone);
        if (email) toAdd.push(email);
    } else if (mode === 'phone') {
        if (phone) toAdd.push(phone);
        else if (email) toAdd.push(email);
    } else if (mode === 'email') {
        if (email) toAdd.push(email);
        else if (phone) toAdd.push(phone);
    }

    if (!toAdd.length) {
        alert('No phone or email available for this customer.');
        return;
    }

    const textarea = document.getElementById('targeted_customers');
    let currentEntries = getTargetedEntries();
    let addedCount = 0;

    toAdd.forEach(val => {
        if (!currentEntries.includes(val)) {
            currentEntries.push(val);
            addedCount++;
        }
    });

    textarea.value = currentEntries.join('\n');
    updateTargetCount();
    syncTableHighlight();
}

function toggleSelectAll(masterCb) {
    const checkboxes = document.querySelectorAll('.customer-row-cb');
    checkboxes.forEach(cb => {
        cb.checked = masterCb.checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.customer-row-cb:checked');
    const count = checked.length;
    document.getElementById('selected_count_text').innerText = `(${count} selected)`;
    document.getElementById('btn_selected_count').innerText = count;
}

function addSelectedToAudience() {
    const checked = document.querySelectorAll('.customer-row-cb:checked');
    if (!checked.length) {
        alert('Please select at least one customer from the table checkboxes.');
        return;
    }

    const mode = document.getElementById('insert_mode_select').value;
    const textarea = document.getElementById('targeted_customers');
    let currentEntries = getTargetedEntries();
    let addedCount = 0;

    checked.forEach(cb => {
        const phone = cb.getAttribute('data-phone') || '';
        const email = cb.getAttribute('data-email') || '';
        let toAdd = [];

        if (mode === 'both') {
            if (phone) toAdd.push(phone);
            if (email) toAdd.push(email);
        } else if (mode === 'phone') {
            if (phone) toAdd.push(phone);
            else if (email) toAdd.push(email);
        } else if (mode === 'email') {
            if (email) toAdd.push(email);
            else if (phone) toAdd.push(phone);
        }

        toAdd.forEach(val => {
            if (val && !currentEntries.includes(val)) {
                currentEntries.push(val);
                addedCount++;
            }
        });
    });

    textarea.value = currentEntries.join('\n');
    updateTargetCount();
    syncTableHighlight();

    // Uncheck select all and row checkboxes
    document.getElementById('selectAllCheckbox').checked = false;
    checked.forEach(cb => cb.checked = false);
    updateSelectedCount();
}
</script>

</main>
</body>
</html>
