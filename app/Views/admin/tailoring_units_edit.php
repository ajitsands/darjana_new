<?php include __DIR__ . '/header.php'; ?>

<div class="admin-main">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 26px;">Edit Tailoring Unit</h1>
        <p style="color: #718096; font-size: 14px;">Update details for this tailoring unit.</p>
    </div>

    <?php if (isset($_SESSION['admin_error'])): ?>
        <div style="background-color: #fde8e8; color: #9b1c1c; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($_SESSION['admin_error']) ?>
        </div>
        <?php unset($_SESSION['admin_error']); ?>
    <?php endif; ?>

    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px;">
        <form method="POST" action="<?= BASE_URL ?>/admin/tailoring-units/update/<?= $unit['id'] ?>" id="tailoringUnitForm">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="unique_unit_code">Unique Unit Code</label>
                    <input type="text" id="unique_unit_code" name="unique_unit_code" required value="<?= htmlspecialchars($unit['unique_unit_code']) ?>" readonly style="background-color: #f3f4f6; cursor: not-allowed; color: #718096;">
                    <span style="font-size: 12px; color: #718096; margin-top: 5px; display: inline-block;">Unit code cannot be changed once created.</span>
                </div>
                <div class="form-group">
                    <label for="unit_name">Unit Name *</label>
                    <input type="text" id="unit_name" name="unit_name" required value="<?= htmlspecialchars($unit['unit_name']) ?>">
                </div>
                <div class="form-group">
                    <label for="contact_person">Contact Person</label>
                    <input type="text" id="contact_person" name="contact_person" value="<?= htmlspecialchars($unit['contact_person']) ?>">
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($unit['contact_number']) ?>">
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="email_id">Email ID</label>
                    <input type="email" id="email_id" name="email_id" value="<?= htmlspecialchars($unit['email_id']) ?>">
                </div>
            </div>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #edf2f7;">
                <button type="submit" class="btn-primary" id="submitBtn">Update Tailoring Unit</button>
                <a href="<?= BASE_URL ?>/admin/tailoring-units" style="margin-left: 15px; color: #718096; text-decoration: none; font-weight: 600;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
