
<main class="main-content">
    <div class="container" style="max-width: 600px; padding: 60px 20px; text-align: center;">
        <?php if ($success): ?>
            <div style="background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 8px; padding: 40px; margin-bottom: 30px;">
                <h1 style="color: #2f855a; font-size: 28px; margin-bottom: 15px;"><?= __('payment_success_title') ?></h1>
                <p style="font-size: 16px; color: #4a5568; margin-bottom: 20px;"><?= __('payment_success_desc') ?></p>
                <div style="font-size: 20px; font-weight: bold; padding: 15px; background: #fff; border: 2px dashed #9ae6b4; display: inline-block;">
                    <?= __('order_number_label') ?> <?= htmlspecialchars($order['order_number'] ?? 'N/A') ?>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/" class="btn-primary" style="padding: 14px 30px; font-size: 16px;"><?= __('continue_shopping') ?></a>
        <?php else: ?>
            <div style="background: #fff5f5; border: 1px solid #fed7d7; border-radius: 8px; padding: 40px; margin-bottom: 30px;">
                <h1 style="color: #c53030; font-size: 28px; margin-bottom: 15px;"><?= __('payment_failed_title') ?></h1>
                <p style="font-size: 16px; color: #4a5568; margin-bottom: 20px;"><?= __('payment_failed_desc') ?></p>
                <div style="font-size: 16px; padding: 15px; background: #fff; border: 1px solid #feb2b2; display: inline-block; color: #c53030;">
                    <?= __('reason_label') ?> <?= htmlspecialchars($resultMessage) ?>
                </div>
                <p style="font-size: 14px; color: #718096; margin-top: 20px;"><?= __('contact_support_desc') ?></p>
            </div>
            <a href="<?= BASE_URL ?>/checkout" class="btn-primary" style="padding: 14px 30px; font-size: 16px;"><?= __('try_again') ?></a>
        <?php endif; ?>
    </div>
</main>

