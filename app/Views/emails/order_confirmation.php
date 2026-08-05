<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 20px; }
        .header img { max-width: 150px; }
        .title { font-size: 24px; color: #b59b54; margin-bottom: 10px; font-weight: bold; }
        .subtitle { font-size: 16px; color: #666; margin-bottom: 20px; }
        .order-info { background: #fdfbf7; border: 1px solid #e8e3d3; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .order-info p { margin: 5px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; padding: 10px; border-bottom: 2px solid #eee; color: #666; font-size: 12px; text-transform: uppercase; }
        td { padding: 15px 10px; border-bottom: 1px solid #eee; vertical-align: top; font-size: 14px; }
        .item-name { font-weight: bold; color: #333; display: block; margin-bottom: 5px; }
        .item-meta { font-size: 12px; color: #777; }
        .totals { width: 100%; text-align: right; margin-top: 20px; }
        .totals p { margin: 5px 0; font-size: 14px; color: #555; }
        .total-row { font-size: 18px; font-weight: bold; color: #b59b54; border-top: 2px solid #eee; padding-top: 10px; margin-top: 10px; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Ensure BASE_URL is set in environment or hardcode the production URL for images -->
            <h1 style="color: #b59b54; margin:0;">DAR JANA FASHION</h1>
        </div>
        
        <div class="title">Thank you for your order!</div>
        <div class="subtitle">Hi <?= htmlspecialchars($order['customer_name'] ?? 'Customer') ?>, we have received your order and are getting it ready.</div>
        
        <div class="order-info">
            <p><strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
            <p><strong>Order Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
            <p><strong>Shipping Address:</strong><br>
                <?= htmlspecialchars($order['address']) ?>,<br>
                <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['country']) ?><br>
                <?= htmlspecialchars($order['phone']) ?>
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                        <div class="item-meta">
                            <?php if (!empty($item['size'])): ?> Size: <?= htmlspecialchars($item['size']) ?><br> <?php endif; ?>
                            <?php if (!empty($item['color'])): ?> Color: <?= htmlspecialchars($item['color']) ?><br> <?php endif; ?>
                            <?php if (!empty($item['length'])): ?> Length: <?= htmlspecialchars($item['length']) ?><br> <?php endif; ?>
                        </div>
                    </td>
                    <td style="text-align:center;"><?= (int)$item['quantity'] ?></td>
                    <td style="text-align:right; font-weight:bold;"><?= number_format($item['price'] * $item['quantity'], 2) ?> BHD</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <?php 
                $subtotal = 0;
                foreach($items as $i) $subtotal += ($i['price'] * $i['quantity']);
                $discount = $order['discount_amount'] ?? 0;
            ?>
            <p>Subtotal: <?= number_format($subtotal, 2) ?> BHD</p>
            <?php if ($discount > 0): ?>
            <p style="color: green;">Discount: -<?= number_format($discount, 2) ?> BHD</p>
            <?php endif; ?>
            <p class="total-row">Total: <?= number_format($order['total_amount'], 2) ?> BHD</p>
        </div>

        <div class="footer">
            If you have any questions, reply to this email or contact us at info@darjanafashion.com.<br>
            &copy; <?= date('Y') ?> Dar Jana Fashion. All rights reserved.
        </div>
    </div>
</body>
</html>
