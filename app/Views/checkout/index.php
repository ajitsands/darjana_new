<style>
    .checkout-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 40px;
        align-items: start;
    }
    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        .container {
            padding: 30px 15px 40px !important;
        }
        #checkoutForm > div {
            padding: 20px !important;
        }
        .address-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="container" style="padding: 60px 20px 80px;">
    <h1 style="font-size: 30px; text-align: center; margin-bottom: 10px;">Express Checkout</h1>
    <p style="text-align: center; color: var(--color-text-muted); margin-bottom: 40px;">Complete your purchase for Dar Jana Fashion luxury dresses & abayas</p>

    <div id="checkoutSuccessMessage" style="display: none; text-align: center; padding: 40px 20px; background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 8px; margin-bottom: 30px;">
    <h2 style="color: #2f855a; margin-bottom: 15px;">Order Placed Successfully!</h2>
    <p style="font-size: 16px; margin-bottom: 20px; color: #4a5568;">Your order <strong id="orderNumberDisplay" style="font-size: 18px;"></strong> has been placed.</p>
    <a href="<?= BASE_URL ?>/" class="btn-primary" style="padding: 12px 24px;">Continue Shopping</a>
</div>

<div id="paymentWidgetContainer"></div>

<div id="checkoutMainContainer">
    <form id="checkoutForm" method="POST" class="checkout-grid">
        <!-- Shipping Form Column -->
        <div style="background-color: #fff; border: 1px solid var(--color-border); padding: 36px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">Billing Information</h3>
            
            <div class="address-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">PHONE / WHATSAPP *</label>
                    <div style="display: flex; gap: 8px;">
                        <select name="phone_code" id="phone_code" style="padding: 12px; border: 1px solid var(--color-border); background: #fff; width: 110px; flex-shrink: 0; font-size: 14px;">
                            <option value="+973" selected>🇧🇭 +973</option>
                            <option value="+965">🇰🇼 +965</option>
                            <option value="+966">🇸🇦 +966</option>
                            <option value="+971">🇦🇪 +971</option>
                            <option value="+974">🇶🇦 +974</option>
                            <option value="+968">🇴🇲 +968</option>
                        </select>
                        <input type="tel" name="phone" id="phone" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border);" placeholder="33000000">
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">EMAIL ADDRESS *</label>
                    <input type="email" name="email" id="email" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border);" placeholder="maryam@example.com">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">FULL NAME *</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border);" placeholder="e.g. Maryam Al-Sabah">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">BILLING ADDRESS *</label>
                <textarea name="address" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border); height: 90px;" placeholder="Block, Street, House / Apartment Number..."></textarea>
            </div>

            <div class="address-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">CITY / REGION *</label>
                    <input type="text" name="city" value="Manama, Bahrain" required style="width: 100%; padding: 12px; border: 1px solid var(--color-border);">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">COUNTRY *</label>
                    <select name="country" id="billing_country" style="width: 100%; padding: 12px; border: 1px solid var(--color-border); background: #fff;">
                        <option value="Bahrain" selected>Bahrain (البحرين)</option>
                        <option value="Kuwait">Kuwait (الكويت)</option>
                        <option value="Saudi Arabia">Saudi Arabia (المملكة العربية السعودية)</option>
                        <option value="UAE">United Arab Emirates (الإمارات العربية المتحدة)</option>
                        <option value="Qatar">Qatar (قطر)</option>
                        <option value="Oman">Oman (عُمان)</option>
                    </select>
                </div>
            </div>

            <!-- Ship to Different Address Toggle -->
            <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border);">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; font-size: 14px;">
                    <input type="checkbox" name="different_shipping" id="different_shipping" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    Ship to a different address?
                </label>
            </div>

            <!-- Shipping Fields (Hidden by default) -->
            <div id="shipping_fields" style="display: none;">
                <h3 style="font-size: 16px; margin-bottom: 16px; color: #4a5568;">Shipping Address</h3>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">DELIVERY ADDRESS *</label>
                    <textarea name="shipping_address" id="shipping_address" style="width: 100%; padding: 12px; border: 1px solid var(--color-border); height: 90px;" placeholder="Block, Street, House / Apartment Number..."></textarea>
                </div>

                <div class="address-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">CITY / REGION *</label>
                        <input type="text" name="shipping_city" id="shipping_city" value="Manama, Bahrain" style="width: 100%; padding: 12px; border: 1px solid var(--color-border);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">COUNTRY *</label>
                        <select name="shipping_country" id="shipping_country" style="width: 100%; padding: 12px; border: 1px solid var(--color-border); background: #fff;">
                            <option value="Bahrain" selected>Bahrain (البحرين)</option>
                            <option value="Kuwait">Kuwait (الكويت)</option>
                            <option value="Saudi Arabia">Saudi Arabia (المملكة العربية السعودية)</option>
                            <option value="UAE">United Arab Emirates (الإمارات العربية المتحدة)</option>
                            <option value="Qatar">Qatar (قطر)</option>
                            <option value="Oman">Oman (عُمان)</option>
                        </select>
                    </div>
                </div>
            </div>


            <!-- Order Note -->
            <div style="margin-bottom: 24px; padding-top: 16px; border-top: 1px solid var(--color-border);">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">ORDER NOTE (OPTIONAL)</label>
                <textarea name="order_note" style="width: 100%; padding: 12px; border: 1px solid var(--color-border); height: 70px;" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
            </div>

            <?php if (isset($afs_gateway_enabled) && $afs_gateway_enabled): ?>
                <button type="submit" class="btn-primary btn-buy-now" style="width: 100%; padding: 18px; font-size: 14px;">Complete Order and Pay Now</button>
            <?php else: ?>
                <button type="submit" class="btn-primary btn-buy-now" style="width: 100%; padding: 18px; font-size: 14px;">Complete Order & Pay Cash / KNET on Delivery</button>
            <?php endif; ?>
        </div>

        <!-- Order Items Breakdown Sidebar -->
        <div style="background-color: var(--color-bg-light); border: 1px solid var(--color-border); padding: 30px;">
            <h3 style="font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">Your Order</h3>
            
            <div style="margin-bottom: 20px; max-height: 350px; overflow-y: auto;">
                <?php foreach ($cart as $item): ?>
                    <div style="display: flex; gap: 14px; margin-bottom: 16px; border-bottom: 1px solid #eee; padding-bottom: 14px;">
                        <img src="<?= str_replace('/high/', '/thumb/', $item['image']) ?>" style="width: 55px; height: 70px; object-fit: cover;">
                        <div style="flex-grow: 1;">
                            <div style="font-size: 13px; font-weight: 600; line-height: 1.3;"><?= htmlspecialchars($item['name']) ?></div>
                            <div style="font-size: 11px; color: #666;">
                                Size: <?= htmlspecialchars($item['size']) ?> | 
                                Color: <?= htmlspecialchars($item['color'] ?? 'N/A') ?> | 
                                Length: <?= htmlspecialchars($item['length'] ?? 'N/A') ?>" | 
                                Qty: <?= $item['quantity'] ?>
                            </div>
                            <div style="font-weight: 700; font-size: 13px; color: var(--color-accent); margin-top: 4px;"><?= number_format($item['price'] * $item['quantity'], 2) ?> BHD</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="border-top: 1px solid var(--color-border); padding-top: 16px; margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">PROMO CODE (OPTIONAL)</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="coupon_code_input" placeholder="Enter offer code" style="flex-grow: 1; padding: 10px; border: 1px solid var(--color-border);">
                    <button type="button" id="apply_coupon_btn" style="padding: 10px 16px; background-color: #4a5568; color: #fff; border: none; cursor: pointer;">Apply</button>
                </div>
                <div id="coupon_message" style="font-size: 12px; margin-top: 5px;"></div>
                <input type="hidden" name="coupon_code" id="applied_coupon_code">
            </div>

            <div style="border-top: 2px solid var(--color-primary); padding-top: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 15px; margin-bottom: 8px;">
                    <span>Subtotal</span>
                    <span id="display_subtotal" data-value="<?= $subtotal ?>"><?= number_format($subtotal, 2) ?> BHD</span>
                </div>
                <div id="discount_row" style="display: none; justify-content: space-between; font-size: 15px; margin-bottom: 8px; color: #38a169;">
                    <span>Discount</span>
                    <span>-<span id="display_discount">0.00</span> BHD</span>
                </div>
                
                <?php if ($vatPercentage > 0 && $vatType !== 'none'): ?>
                <div style="display: flex; justify-content: space-between; font-size: 15px; margin-bottom: 8px; color: #666;">
                    <span>VAT (<?= $vatPercentage ?>% - <?= ucfirst($vatType) ?>)</span>
                    <span id="display_vat" data-value="<?= $vatAmount ?>"><?= number_format($vatAmount, 2) ?> BHD</span>
                </div>
                <?php endif; ?>

                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; margin-top: 14px; color: var(--color-primary);">
                    <span>Total</span>
                    <span style="color: var(--color-accent);"><span id="display_total" data-value="<?= $total ?>"><?= number_format($total, 2) ?></span> BHD</span>
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
    const differentShippingCheckbox = document.getElementById('different_shipping');
    const shippingFields = document.getElementById('shipping_fields');
    
    // Link country selection to phone code
    const billingCountry = document.getElementById('billing_country');
    const phoneCode = document.getElementById('phone_code');
    const countryToPhoneCode = {
        'Bahrain': '+973',
        'Kuwait': '+965',
        'Saudi Arabia': '+966',
        'UAE': '+971',
        'Qatar': '+974',
        'Oman': '+968'
    };

    if (billingCountry && phoneCode) {
        billingCountry.addEventListener('change', function() {
            const code = countryToPhoneCode[this.value];
            if (code) {
                phoneCode.value = code;
            }
        });
    }

    if (differentShippingCheckbox) {
        differentShippingCheckbox.addEventListener('change', function () {
            if (this.checked) {
                shippingFields.style.display = 'block';
                // Make fields required when shown
                document.getElementById('shipping_address').setAttribute('required', 'required');
                document.getElementById('shipping_city').setAttribute('required', 'required');
            } else {
                shippingFields.style.display = 'none';
                // Remove required when hidden
                document.getElementById('shipping_address').removeAttribute('required');
                document.getElementById('shipping_city').removeAttribute('required');
            }
        });
    }

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
                    if (data.is_payment && data.checkout_id) {
                        checkoutForm.style.display = 'none';
                        const pContainer = document.getElementById('paymentWidgetContainer');
                        pContainer.innerHTML = `
                            <div style="padding: 40px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                <h2 style="font-size: 20px; margin-bottom: 20px;">Complete Your Payment</h2>
                                <p style="margin-bottom: 20px; color: #4a5568;">Please enter your payment details below to complete order <strong>${data.order_number}</strong>.</p>
                                <form action="${window.BASE_URL}/checkout/payment-result?order_id=${data.order_id}" class="paymentWidgets" data-brands="VISA MASTER AMEX"></form>
                            </div>
                        `;
                        const script = document.createElement('script');
                        script.src = data.payment_script;
                        document.body.appendChild(script);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        checkoutForm.style.display = 'none';
                        orderNumberDisplay.textContent = data.order_number;
                        checkoutSuccessMessage.style.display = 'block';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                } else {
                    alert(data.message || 'Error processing order.');
                }
            })
            .catch(err => alert('Network error occurred.'));
        });
    }

    const applyCouponBtn = document.getElementById('apply_coupon_btn');
    const couponCodeInput = document.getElementById('coupon_code_input');
    const appliedCouponCode = document.getElementById('applied_coupon_code');
    const couponMessage = document.getElementById('coupon_message');
    
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            const code = couponCodeInput ? couponCodeInput.value.trim() : '';
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const phoneCodeInput = document.getElementById('phone_code');
            
            const email = emailInput ? emailInput.value.trim() : '';
            const phone = phoneInput ? phoneInput.value.trim() : '';
            const phoneCode = phoneCodeInput ? phoneCodeInput.value.trim() : '';
            
            if (!code) {
                couponMessage.textContent = 'Please enter a coupon code.';
                couponMessage.style.color = '#e53e3e';
                return;
            }
            if (!email && !phone) {
                couponMessage.textContent = 'Please enter your phone number or email address first to validate offer.';
                couponMessage.style.color = '#e53e3e';
                return;
            }

            couponMessage.textContent = 'Validating code...';
            couponMessage.style.color = '#4a5568';

            const formData = new FormData();
            formData.append('coupon_code', code);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('phone_code', phoneCode);

            fetch(window.BASE_URL + '/checkout/apply-coupon', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    couponMessage.textContent = '✓ ' + data.message;
                    couponMessage.style.color = '#2f855a';
                    appliedCouponCode.value = code;
                    couponCodeInput.setAttribute('disabled', 'disabled');
                    applyCouponBtn.textContent = 'Applied';
                    applyCouponBtn.setAttribute('disabled', 'disabled');
                    applyCouponBtn.style.backgroundColor = '#38a169';
                    
                    const discount = parseFloat(data.discount_amount);
                    document.getElementById('discount_row').style.display = 'flex';
                    document.getElementById('display_discount').textContent = discount.toFixed(2);
                    
                    const subtotalEl = document.getElementById('display_subtotal');
                    const subtotal = parseFloat(subtotalEl.getAttribute('data-value'));
                    
                    let newSubtotal = subtotal - discount;
                    if (newSubtotal < 0) newSubtotal = 0;
                    
                    const vatType = '<?= $vatType ?>';
                    const vatPercentage = <?= $vatPercentage ?>;
                    
                    let newVat = 0;
                    let newTotal = 0;
                    
                    if (vatType === 'none') {
                        newTotal = newSubtotal;
                    } else if (vatType === 'inclusive') {
                        newTotal = newSubtotal;
                        let innerSubtotal = newSubtotal / (1 + (vatPercentage / 100));
                        newVat = newSubtotal - innerSubtotal;
                    } else {
                        newVat = newSubtotal * (vatPercentage / 100);
                        newTotal = newSubtotal + newVat;
                    }
                    
                    const vatEl = document.getElementById('display_vat');
                    if (vatEl) {
                        vatEl.textContent = newVat.toFixed(2) + ' BHD';
                    }
                    
                    document.getElementById('display_total').textContent = newTotal.toFixed(2);
                    
                } else {
                    couponMessage.textContent = data.message;
                    couponMessage.style.color = '#e53e3e';
                    appliedCouponCode.value = '';
                }
            })
            .catch(err => {
                console.error(err);
                couponMessage.textContent = 'Error applying coupon.';
                couponMessage.style.color = '#e53e3e';
            });
        });
    }
});
</script>
