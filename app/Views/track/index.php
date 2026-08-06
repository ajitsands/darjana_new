<main class="main-content" style="padding-top: 100px; padding-bottom: 80px; background-color: #fcfbfa; min-height: 80vh;">
    <div class="full-width-container" style="max-width: 700px; margin: 0 auto;">
        
        <h1 style="font-family: var(--heading-font-family); font-size: 28px; text-align: center; margin-bottom: 30px; letter-spacing: 0.1em; color: var(--color-primary);">TRACK YOUR ORDER</h1>
        
        <div style="background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 40px;">
            <p style="text-align: center; color: #718096; margin-bottom: 24px; font-size: 15px;">Enter your Dar Jana Fashion order number to check its current status.</p>
            
            <form action="<?= BASE_URL ?>/track-order" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="order_number" value="<?= htmlspecialchars($orderNumber) ?>" placeholder="e.g., DJF-A1B2C3" required style="flex: 1; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 16px; font-family: var(--body-font-family);">
                <button type="submit" class="btn-primary" style="padding: 12px 24px; font-size: 15px; letter-spacing: 0.1em;">TRACK</button>
            </form>

            <?php if (!empty($error)): ?>
                <div style="margin-top: 20px; padding: 12px; background: #fee2e2; color: #b91c1c; border-radius: 4px; font-size: 14px; text-align: center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($order): ?>
            <div style="background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                <h2 style="font-family: var(--heading-font-family); font-size: 20px; margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">ORDER DETAILS: <?= htmlspecialchars($order['order_number']) ?></h2>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <div style="font-size: 12px; color: #718096; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em; margin-bottom: 4px;">Order Date</div>
                        <div style="font-size: 15px; font-weight: 600;"><?= date('M d, Y - h:i A', strtotime($order['created_at'])) ?></div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; color: #718096; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em; margin-bottom: 4px;">Order Status</div>
                        <?php
                            $status = $order['status'] ?? 'New';
                            $statusBg = '#e2e8f0'; $statusColor = '#4a5568';
                            if ($status === 'New') { $statusBg = '#dbeafe'; $statusColor = '#1d4ed8'; }
                            if ($status === 'Processing') { $statusBg = '#fef3c7'; $statusColor = '#b45309'; }
                            if ($status === 'Shipped') { $statusBg = '#dcfce7'; $statusColor = '#15803d'; }
                            if ($status === 'Delivered') { $statusBg = '#bbf7d0'; $statusColor = '#166534'; }
                            if ($status === 'Canceled') { $statusBg = '#fee2e2'; $statusColor = '#b91c1c'; }
                        ?>
                        <div style="background: <?= $statusBg ?>; color: <?= $statusColor ?>; padding: 6px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; display: inline-block;">
                            <?= htmlspecialchars($status) ?>
                        </div>
                    </div>
                </div>

                <?php if ($status === 'Shipped'): ?>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 20px; margin-bottom: 24px;">
                        <h3 style="font-family: var(--heading-font-family); font-size: 16px; margin-bottom: 16px; color: var(--color-accent); letter-spacing: 0.05em;">SHIPPING INFORMATION</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <div style="font-size: 12px; color: #718096; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Provider</div>
                                <div style="font-size: 15px; font-weight: 600;"><?= htmlspecialchars($order['shipping_provider'] ?: 'Standard Shipping') ?></div>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #718096; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Tracking Number</div>
                                <div style="font-size: 15px; font-weight: 600;"><?= htmlspecialchars($order['tracking_number'] ?: 'Pending') ?></div>
                            </div>
                        </div>

                        <?php if (!empty($order['shipping_attachment'])): ?>
                            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                                <a href="<?= BASE_URL ?>/public/uploads/shipping/<?= htmlspecialchars($order['shipping_attachment']) ?>" target="_blank" class="btn-primary" style="display: inline-block; padding: 8px 16px; font-size: 13px; text-decoration: none; border-radius: 4px; background-color: var(--color-primary); color: white;">
                                    View Shipping Receipt / Waybill
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 30px;">
                    <h3 style="font-family: var(--heading-font-family); font-size: 16px; margin-bottom: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Order Items</h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <?php foreach ($orderItems as $item): ?>
                            <li style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                                <div>
                                    <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($item['product_name']) ?></div>
                                    <div style="font-size: 12px; color: #718096; margin-top: 4px;">
                                        Qty: <?= $item['quantity'] ?> | Size: <?= htmlspecialchars($item['size']) ?>
                                        <?php if($item['color']) echo ' | Color: ' . htmlspecialchars($item['color']); ?>
                                    </div>
                                </div>
                                <div style="font-weight: 600; font-size: 14px;">
                                    <?= number_format($item['price'] * $item['quantity'], 2) ?> BHD
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>
