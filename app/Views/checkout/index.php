<div class="container" style="padding: 60px 20px 80px;">
    <h1 style="font-size: 30px; text-align: center; margin-bottom: 10px;">Express Checkout</h1>
    <p style="text-align: center; color: var(--color-text-muted); margin-bottom: 40px;">Complete your purchase for Dar Jana Fashion luxury dresses & abayas</p>

    <div id="checkoutSuccessMessage" style="display: none; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 40px; text-align: center; max-width: 600px; margin: 0 auto;">
        <svg width="48" height="48" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24" style="margin: 0 auto 16px;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <h2 style="font-size: 24px; color: #15803d; margin-bottom: 10px;">Order Placed Successfully!</h2>
        <p style="font-size: 16px; color: #374151; margin-bottom: 16px;">Your order number is <strong id="orderNumberDisplay" style="color: var(--color-primary);"></strong></p>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 24px;">Thank you for shopping with Dar Jana Fashion. A confirmation email and tracking updates have been dispatched.</p>
        <a href="<?= BASE_URL ?>/collections/all-abaya" class="btn-primary">Continue Shopping</a>
    </div>

    <form id="checkoutForm" style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 40px; align-items: start;">
        <!-- Shipping Form Column -->
        <div style="background-color: #fff; border: 1px solid var(--color-border); padding: 36px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">Customer Delivery Information</h3>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">FULL NAME *</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border);" placeholder="e.g. Maryam Al-Sabah">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">EMAIL ADDRESS *</label>
                    <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border);" placeholder="maryam@example.com">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">PHONE / WHATSAPP *</label>
                    <input type="tel" name="phone" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border);" placeholder="+965 99000000">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">DELIVERY ADDRESS *</label>
                <textarea name="address" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border); height: 90px;" placeholder="Block, Street, House / Apartment Number..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">CITY / REGION *</label>
                    <input type="text" name="city" value="Kuwait City" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border);">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">COUNTRY *</label>
                    <select name="country" style="width: 100%; padding: 12px; border: 1px solid var(--color-border); background: #fff;">
                        <option value="Kuwait" selected>Kuwait (الكويت)</option>
                        <option value="Saudi Arabia">Saudi Arabia (المملكة العربية السعودية)</option>
                        <option value="UAE">United Arab Emirates (الإمارات العربية المتحدة)</option>
                        <option value="Qatar">Qatar (قطر)</option>
                        <option value="Bahrain">Bahrain (البحرين)</option>
                        <option value="Oman">Oman (عُمان)</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-primary btn-buy-now" style="width: 100%; padding: 18px; font-size: 14px;">Complete Order & Pay Cash / KNET on Delivery</button>
        </div>

        <!-- Order Items Breakdown Sidebar -->
        <div style="background-color: var(--color-bg-light); border: 1px solid var(--color-border); padding: 30px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">Your Order</h3>
            
            <div style="margin-bottom: 20px; max-height: 350px; overflow-y: auto;">
                <?php foreach ($cart as $item): ?>
                    <div style="display: flex; gap: 14px; margin-bottom: 16px; border-bottom: 1px solid #eee; padding-bottom: 14px;">
                        <img src="<?= $item['image'] ?>" style="width: 55px; height: 70px; object-fit: cover;">
                        <div style="flex-grow: 1;">
                            <div style="font-size: 13px; font-weight: 600; line-height: 1.3;"><?= htmlspecialchars($item['name']) ?></div>
                            <div style="font-size: 11px; color: #666;">Size: <?= $item['size'] ?> | Qty: <?= $item['quantity'] ?></div>
                            <div style="font-weight: 700; font-size: 13px; color: var(--color-accent); margin-top: 4px;"><?= number_format($item['price'] * $item['quantity'], 2) ?> BHD</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="border-top: 2px solid var(--color-primary); padding-top: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 15px; margin-bottom: 8px;">
                    <span>Subtotal</span>
                    <span><?= number_format($total, 2) ?> BHD</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 15px; margin-bottom: 8px;">
                    <span>Express Shipping</span>
                    <span style="color: var(--color-accent); font-weight: 700;">FREE</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; margin-top: 14px; color: var(--color-primary);">
                    <span>Total</span>
                    <span style="color: var(--color-accent);"><?= number_format($total, 2) ?> BHD</span>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkoutForm = document.getElementById('checkoutForm');
    const checkoutSuccessMessage = document.getElementById('checkoutSuccessMessage');
    const orderNumberDisplay = document.getElementById('orderNumberDisplay');

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(window.BASE_URL + '/checkout/process', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    checkoutForm.style.display = 'none';
                    orderNumberDisplay.textContent = data.order_number;
                    checkoutSuccessMessage.style.display = 'block';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert(data.message || 'Error processing order.');
                }
            })
            .catch(err => alert('Network error occurred.'));
        });
    }
});
</script>
