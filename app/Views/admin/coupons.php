<?php include __DIR__ . '/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 26px;">Offer Codes / Coupons</h1>
            <p style="color: #718096; font-size: 14px;">Manage discount codes for all or targeted customer groups</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/admin/coupons/create" class="btn-primary" style="display: inline-block; text-decoration: none;">+ Add New Coupon</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div style="background-color: #c6f6d5; border-left: 4px solid #48bb78; color: #2f855a; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div style="background-color: #fed7d7; border-left: 4px solid #e53e3e; color: #c53030; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="table-responsive" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <?php if (empty($coupons)): ?>
            <p style="color: #718096; text-align: center; padding: 20px;">No coupons found.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-border); text-align: left;">
                        <th style="padding: 12px; font-weight: 600;">Code</th>
                        <th style="padding: 12px; font-weight: 600;">Discount</th>
                        <th style="padding: 12px; font-weight: 600;">Audience</th>
                        <th style="padding: 12px; font-weight: 600;">Validity</th>
                        <th style="padding: 12px; font-weight: 600; text-align: center;">Limit / User</th>
                        <th style="padding: 12px; font-weight: 600;">Created</th>
                        <th style="padding: 12px; font-weight: 600; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $coupon): ?>
                        <?php 
                        $isTargeted = ($coupon['audience_type'] ?? 'all') === 'targeted';
                        $targetCount = 0;
                        if ($isTargeted && !empty($coupon['targeted_customers'])) {
                            $targetCount = count(preg_split('/[,\r\n;]+/', $coupon['targeted_customers'], -1, PREG_SPLIT_NO_EMPTY));
                        }
                        ?>
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <td style="padding: 12px;">
                                <strong style="font-family: monospace; font-size: 15px; background: #edf2f7; padding: 4px 8px; border-radius: 4px;"><?= htmlspecialchars($coupon['code']) ?></strong>
                            </td>
                            <td style="padding: 12px;">
                                <span style="font-weight: 600; color: #2c5282;">
                                    <?= number_format($coupon['discount_value'], 2) ?>
                                    <?= $coupon['discount_type'] === 'percentage' ? '%' : 'BHD' ?>
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                <?php if ($isTargeted): ?>
                                    <span style="display: inline-block; padding: 4px 10px; background: #faf5ff; color: #6b46c1; border: 1px solid #d6bcfa; border-radius: 12px; font-size: 12px; font-weight: 600;" title="<?= htmlspecialchars($coupon['targeted_customers'] ?? '') ?>">
                                        🎯 Targeted (<?= $targetCount ?>)
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 4px 10px; background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                        🌐 All Customers
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px;">
                                <div style="font-size: 13px; color: #4a5568;">Start: <?= htmlspecialchars($coupon['start_date']) ?></div>
                                <div style="font-size: 13px; color: #4a5568;">End: <?= htmlspecialchars($coupon['end_date']) ?></div>
                            </td>
                            <td style="padding: 12px; text-align: center; font-weight: 600;"><?= htmlspecialchars($coupon['usage_limit_per_user']) ?></td>
                            <td style="padding: 12px; font-size: 13px; color: #718096;"><?= date('M j, Y', strtotime($coupon['created_at'])) ?></td>
                            <td style="padding: 12px; text-align: right; white-space: nowrap;">
                                <a href="<?= BASE_URL ?>/admin/coupons/edit/<?= $coupon['id'] ?>" style="display: inline-block; text-decoration: none; padding: 6px 12px; font-size: 12px; background: #edf2f7; color: #4a5568; border-radius: 4px; margin-right: 6px; font-weight: 500;">Edit</a>
                                <a href="<?= BASE_URL ?>/admin/coupons/delete/<?= $coupon['id'] ?>" class="btn-danger" style="display: inline-block; text-decoration: none; padding: 6px 12px; font-size: 12px;" onclick="return confirmDelete(event, '<?= BASE_URL ?>/admin/coupons/delete/<?= $coupon['id'] ?>', 'Are you sure you want to delete this coupon?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>
</body>
</html>
