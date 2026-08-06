<?php include __DIR__ . '/header.php'; ?>

<div class="admin-main">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 26px;">Add Tailoring Unit</h1>
        <p style="color: #718096; font-size: 14px;">Register a new tailoring unit.</p>
    </div>

    <?php if (isset($_SESSION['admin_error'])): ?>
        <div style="background-color: #fde8e8; color: #9b1c1c; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($_SESSION['admin_error']) ?>
        </div>
        <?php unset($_SESSION['admin_error']); ?>
    <?php endif; ?>

    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px;">
        <form method="POST" action="<?= BASE_URL ?>/admin/tailoring-units/store" id="tailoringUnitForm">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="unique_unit_code">Unique Unit Code *</label>
                    <input type="text" id="unique_unit_code" name="unique_unit_code" required placeholder="e.g. TU-1001">
                    <span id="code_error" style="color: #e53e3e; font-size: 12px; font-weight: 600; display: none; margin-top: 5px;">This code is already in use!</span>
                </div>
                <div class="form-group">
                    <label for="unit_name">Unit Name *</label>
                    <input type="text" id="unit_name" name="unit_name" required placeholder="e.g. Al-Aseel Tailors">
                </div>
                <div class="form-group">
                    <label for="contact_person">Contact Person</label>
                    <input type="text" id="contact_person" name="contact_person">
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number">
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="email_id">Email ID</label>
                    <input type="email" id="email_id" name="email_id">
                </div>
                <div class="form-group" style="grid-column: 1 / -1; display: flex; align-items: center;">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width: auto; margin-right: 10px; margin-bottom: 0;">
                    <label for="is_active" style="margin-bottom: 0; font-weight: 600;">Active (Available for order assignments)</label>
                </div>
            </div>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #edf2f7;">
                <button type="submit" class="btn-primary" id="submitBtn">Save Tailoring Unit</button>
                <a href="<?= BASE_URL ?>/admin/tailoring-units" style="margin-left: 15px; color: #718096; text-decoration: none; font-weight: 600;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('unique_unit_code').addEventListener('blur', function() {
        const code = this.value.trim();
        if (!code) return;
        
        fetch('<?= BASE_URL ?>/admin/tailoring-units/check-code?code=' + encodeURIComponent(code))
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.getElementById('code_error').style.display = 'block';
                    document.getElementById('submitBtn').disabled = true;
                    document.getElementById('submitBtn').style.opacity = '0.5';
                    document.getElementById('unique_unit_code').style.borderColor = '#e53e3e';
                } else {
                    document.getElementById('code_error').style.display = 'none';
                    document.getElementById('submitBtn').disabled = false;
                    document.getElementById('submitBtn').style.opacity = '1';
                    document.getElementById('unique_unit_code').style.borderColor = '#e2e8f0';
                }
            });
    });
</script>

</body>
</html>
