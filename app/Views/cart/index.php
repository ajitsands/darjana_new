<div class="container" style="padding: 60px 20px 80px;">
    <h1 style="font-size: 32px; text-align: center; margin-bottom: 40px;">Your Shopping Cart</h1>

    <?php if (empty($cart)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <p style="color: #666; font-size: 18px; margin-bottom: 24px;">Your cart is currently empty.</p>
            <a href="<?= BASE_URL ?>/collections/all-abaya" class="btn-primary" style="padding: 16px 36px;">Explore Collections</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
            <!-- Cart Items Table -->
            <div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--color-primary); text-align: left; font-family: var(--heading-font-family); font-size: 12px; letter-spacing: 0.15em;">
                            <th style="padding: 14px 0;">PRODUCT</th>
                            <th style="padding: 14px 0;">PRICE</th>
                            <th style="padding: 14px 0;">QTY</th>
                            <th style="padding: 14px 0; text-align: right;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $item): ?>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td style="padding: 20px 0; display: flex; gap: 16px; align-items: center;">
                                    <img src="<?= str_replace('/high/', '/thumb/', $item['image']) ?>" style="width: 70px; height: 90px; object-fit: cover;">
                                    <div>
                                        <a href="<?= BASE_URL ?>/product/<?= $item['slug'] ?>" style="font-weight: 600; color: var(--color-primary); display: block;"><?= htmlspecialchars($item['name']) ?></a>
                                        <div style="font-size: 12px; color: var(--color-text-muted);">
                                            Code: <?= htmlspecialchars($item['product_code'] ?? 'N/A') ?> | 
                                            Size: <?= htmlspecialchars($item['size']) ?> | 
                                            Color: <?= htmlspecialchars($item['color'] ?? 'N/A') ?> | 
                                            Length: <?= htmlspecialchars($item['length'] ?? 'N/A') ?>"
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 20px 0; font-weight: 600;"><?= number_format($item['price'], 2) ?> BHD</td>
                                <td style="padding: 20px 0;">
                                    <div class="qty-btn-group" style="width: max-content;">
                                        <button type="button" class="qty-btn btn-qty-minus" data-key="<?= $item['key'] ?>" data-qty="<?= $item['quantity'] - 1 ?>">-</button>
                                        <span class="qty-val"><?= $item['quantity'] ?></span>
                                        <button type="button" class="qty-btn btn-qty-plus" data-key="<?= $item['key'] ?>" data-qty="<?= $item['quantity'] + 1 ?>">+</button>
                                    </div>
                                </td>
                                <td style="padding: 20px 0; text-align: right; font-weight: 700; color: var(--color-accent);">
                                    <?= number_format($item['price'] * $item['quantity'], 2) ?> BHD
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Summary Sidebar -->
            <div style="background-color: var(--color-bg-light); border: 1px solid var(--color-border); padding: 30px;">
                <h3 style="font-size: 18px; margin-bottom: 20px;">Order Summary</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px;">
                    <span>Subtotal</span>
                    <span><?= number_format($total, 2) ?> BHD</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 15px;">
                    <span>Express Delivery</span>
                    <span style="color: var(--color-accent); font-weight: 600;">FREE</span>
                </div>
                <div style="border-top: 1px solid var(--color-border); margin: 16px 0; padding-top: 16px; display: flex; justify-content: space-between; font-size: 18px; font-weight: 700;">
                    <span>Total Amount</span>
                    <span style="color: var(--color-accent);"><?= number_format($total, 2) ?> BHD</span>
                </div>
                <a href="<?= BASE_URL ?>/checkout" class="btn-primary btn-buy-now" style="width: 100%; display: block; text-align: center; margin-top: 20px;">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>
