<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Order Details #<?= htmlspecialchars($order['order_number']) ?></h1>
                    <p style="color: #718096; font-size: 14px;">Placed on <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></p>
                </div>
                <a href="<?= BASE_URL ?>/admin/orders" class="btn-primary" style="background: transparent; color: #181818; border: 1px solid var(--color-border);">← Back to Orders</a>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <!-- Left Column: Items -->
                <div>
                    <div class="admin-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 12px; margin-bottom: 20px;">
                            <h3 style="margin: 0; font-size: 16px;">Order Items</h3>
                            <?php if (!empty($assignments)): ?>
                                <a href="<?= BASE_URL ?>/admin/order/print-process-requests/<?= $order['id'] ?>" target="_blank" class="btn-primary" style="font-size: 12px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 5px;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Print Process Requests
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ITEM INFO</th>
                                        <th>OPTIONS</th>
                                        <th>REMARKS</th>
                                        <th style="text-align: center;">QTY</th>
                                        <th style="text-align: right;">UNIT PRICE</th>
                                        <th style="text-align: right;">TOTAL</th>
                                        <th>ASSIGNMENTS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($item['product_code'])): ?>
                                                    <div style="font-size: 11px; color: #c5a059; font-weight: 700; margin-bottom: 4px;"><?= htmlspecialchars($item['product_code']) ?></div>
                                                <?php endif; ?>
                                                <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($item['product_name']) ?></div>
                                            </td>
                                            <td style="font-size: 13px; color: #4a5568;">
                                                <div><strong>Size:</strong> <?= htmlspecialchars($item['size'] ?? 'N/A') ?></div>
                                                <div><strong>Color:</strong> <?= htmlspecialchars($item['color'] ?: 'N/A') ?></div>
                                                <div><strong>Length:</strong> <?= htmlspecialchars($item['length'] ?: 'N/A') ?>"</div>
                                            </td>
                                            <td style="font-size: 13px; color: #4a5568; max-width: 200px;">
                                                <?= !empty($item['note']) ? nl2br(htmlspecialchars($item['note'])) : '<span style="color: #a0aec0;">None</span>' ?>
                                            </td>
                                            <td style="text-align: center; font-weight: 600;">
                                                <?= $item['quantity'] ?>
                                            </td>
                                            <td style="text-align: right; font-weight: 600; font-size: 13px;">
                                                <?= number_format($item['price'], 2) ?> BHD
                                            </td>
                                            <td style="text-align: right; font-weight: 700;">
                                                <?= number_format($item['price'] * $item['quantity'], 2) ?> BHD
                                            </td>
                                            <td style="min-width: 250px;">
                                                <?php
                                                $itemAssignments = array_filter($assignments, function($a) use ($item) {
                                                    return $a['order_item_id'] == $item['id'];
                                                });
                                                $assignedQty = array_sum(array_column($itemAssignments, 'quantity'));
                                                $remainingQty = $item['quantity'] - $assignedQty;
                                                ?>
                                                
                                                <?php if (!empty($itemAssignments)): ?>
                                                    <div style="margin-bottom: 10px;">
                                                        <?php foreach ($itemAssignments as $assignment): ?>
                                                            <div style="font-size: 12px; background: #f7fafc; padding: 4px 8px; border-radius: 4px; margin-bottom: 4px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                                                <div>
                                                                    <strong><?= htmlspecialchars($assignment['unit_name']) ?></strong> (Qty: <?= $assignment['quantity'] ?>)<br>
                                                                    <span style="color: #718096; font-size: 11px;">PR: <?= htmlspecialchars($assignment['process_number']) ?></span>
                                                                </div>
                                                                <a href="#" onclick="confirmDelete(event, '<?= BASE_URL ?>/admin/order/remove-assignment/<?= $assignment['id'] ?>?order_id=<?= $order['id'] ?>', 'Are you sure you want to remove this assignment?')" style="color: #e53e3e; text-decoration: none; font-size: 11px; padding: 2px 5px; border: 1px solid #e53e3e; border-radius: 3px;">Remove</a>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($order['status'] === 'Processing' && $remainingQty > 0): ?>
                                                    <form method="POST" action="<?= BASE_URL ?>/admin/order/assign-item/<?= $order['id'] ?>" style="display: flex; gap: 5px; align-items: center; background: #fff; padding: 8px; border-radius: 4px; border: 1px dashed #cbd5e0;">
                                                        <input type="hidden" name="order_item_id" value="<?= $item['id'] ?>">
                                                        <select name="tailoring_unit_id" required style="padding: 4px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; max-width: 120px;">
                                                            <option value="">Select Unit...</option>
                                                            <?php foreach ($activeUnits as $unit): ?>
                                                                <option value="<?= $unit['id'] ?>"><?= htmlspecialchars($unit['unit_name']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <input type="number" name="quantity" min="1" max="<?= $remainingQty ?>" value="<?= $remainingQty ?>" required style="padding: 4px; width: 50px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px;" title="Quantity">
                                                        <button type="submit" style="padding: 4px 8px; background: #3182ce; color: #fff; border: none; border-radius: 3px; font-size: 12px; cursor: pointer; font-weight: 600;">Assign</button>
                                                    </form>
                                                <?php elseif ($remainingQty <= 0): ?>
                                                    <span style="font-size: 11px; color: #38a169; font-weight: 600; display: inline-block; padding: 2px 6px; background: #f0fff4; border-radius: 4px; border: 1px solid #9ae6b4;">Fully Assigned</span>
                                                <?php else: ?>
                                                    <span style="font-size: 11px; color: #a0aec0; font-style: italic;">Change status to Processing to assign</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Customer & Summary -->
                <div>
                    <div class="admin-card" style="margin-bottom: 24px;">
                        <h3 style="margin-bottom: 16px; font-size: 16px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">Customer Information</h3>
                        <p style="margin-bottom: 8px;"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p style="margin-bottom: 8px;"><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($order['email']) ?>" style="color: var(--color-accent);"><?= htmlspecialchars($order['email']) ?></a></p>
                        <p style="margin-bottom: 8px;"><strong>Phone:</strong> <a href="tel:<?= htmlspecialchars($order['phone']) ?>" style="color: var(--color-accent);"><?= htmlspecialchars($order['phone']) ?></a></p>
                        
                        <h4 style="margin-top: 16px; margin-bottom: 8px; font-size: 14px; color: #4a5568;">Billing Address</h4>
                        <p style="font-size: 14px; color: #718096; line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($order['address'])) ?><br>
                            <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['country']) ?>
                        </p>
                        
                        <h4 style="margin-top: 16px; margin-bottom: 8px; font-size: 14px; color: #4a5568;">Shipping Address</h4>
                        <?php if(!empty($order['shipping_address'])): ?>
                            <p style="font-size: 14px; color: #718096; line-height: 1.5;">
                                <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                                <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_country']) ?>
                            </p>
                        <?php else: ?>
                            <p style="font-size: 14px; color: #718096; font-style: italic;">Same as Billing Address</p>
                        <?php endif; ?>

                        <?php if(!empty($order['order_note'])): ?>
                            <h4 style="margin-top: 16px; margin-bottom: 8px; font-size: 14px; color: #4a5568;">Order Note</h4>
                            <div style="background-color: #fffbeb; border-left: 3px solid #f59e0b; padding: 10px 12px; font-size: 14px; color: #92400e;">
                                <?= nl2br(htmlspecialchars($order['order_note'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="admin-card">
                        <h3 style="margin-bottom: 16px; font-size: 16px; border-bottom: 1px solid var(--color-border); padding-bottom: 12px;">Order Summary</h3>
                        
                        <?php 
                        $subtotal = 0;
                        if (($order['vat_type'] ?? '') === 'inclusive' && $order['vat_amount'] > 0) {
                            $subtotal = $order['total_amount'] - $order['vat_amount'];
                        } else if (($order['vat_type'] ?? '') === 'exclusive' && $order['vat_amount'] > 0) {
                            $subtotal = $order['total_amount'] - $order['vat_amount'];
                        } else {
                            $subtotal = $order['total_amount'];
                        }
                        ?>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                            <span style="color: #4a5568;">Subtotal</span>
                            <span><?= number_format($subtotal, 2) ?> BHD</span>
                        </div>

                        <?php if (isset($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #38a169;">
                            <span>Discount (<?= htmlspecialchars($order['coupon_code'] ?? 'Coupon') ?>)</span>
                            <span>-<?= number_format($order['discount_amount'], 2) ?> BHD</span>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($order['vat_amount']) && $order['vat_amount'] > 0): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                            <span style="color: #4a5568;">VAT (<?= ucfirst($order['vat_type']) ?>)</span>
                            <span><?= number_format($order['vat_amount'], 2) ?> BHD</span>
                        </div>
                        <?php endif; ?>

                        <div style="display: flex; justify-content: space-between; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--color-border); font-size: 18px; font-weight: 700;">
                            <span>Total</span>
                            <span style="color: var(--color-accent);"><?= number_format($order['total_amount'], 2) ?> BHD</span>
                        </div>
                        
                        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border);">
                            <form method="POST" action="<?= BASE_URL ?>/admin/order/update-status/<?= $order['id'] ?>" enctype="multipart/form-data">
                                <div style="margin-bottom: 12px;">
                                    <label for="payment_status" style="display: block; font-size: 12px; color: #718096; margin-bottom: 6px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Payment Status</label>
                                    <select name="payment_status" id="payment_status" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
                                        <option value="Pending" <?= ($order['payment_status'] ?? 'Pending') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Paid" <?= ($order['payment_status'] ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="Failed" <?= ($order['payment_status'] ?? '') === 'Failed' ? 'selected' : '' ?>>Failed</option>
                                        <option value="Refunded" <?= ($order['payment_status'] ?? '') === 'Refunded' ? 'selected' : '' ?>>Refunded</option>
                                    </select>
                                </div>
                                
                                <div style="margin-bottom: 16px;">
                                    <label for="status" style="display: block; font-size: 12px; color: #718096; margin-bottom: 6px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Order Status</label>
                                    <select name="status" id="status" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;" onchange="toggleShippingFields()">
                                        <option value="New" <?= $order['status'] === 'New' ? 'selected' : '' ?>>New</option>
                                        <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="Shipped" <?= $order['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="Canceled" <?= $order['status'] === 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                                        <?php if($order['status'] === 'Pending'): ?>
                                            <option value="Pending" selected>Pending</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                
                                <!-- Hidden Tracking Fields -->
                                <div id="shippingFields" style="display: <?= $order['status'] === 'Shipped' ? 'block' : 'none' ?>; margin-bottom: 20px; padding: 15px; background: #f9fafb; border: 1px solid #e2e8f0; border-radius: 6px;">
                                    <label style="display: block; font-size: 12px; color: #718096; margin-bottom: 6px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Shipping Provider (e.g. DHL)</label>
                                    <input type="text" name="shipping_provider" value="<?= htmlspecialchars($order['shipping_provider'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; margin-bottom: 12px;">

                                    <label style="display: block; font-size: 12px; color: #718096; margin-bottom: 6px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Tracking Number</label>
                                    <input type="text" name="tracking_number" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; margin-bottom: 12px;">

                                    <label style="display: block; font-size: 12px; color: #718096; margin-bottom: 6px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Shipping Attachment (Receipt/Waybill)</label>
                                    <?php if (!empty($order['shipping_attachment'])): ?>
                                        <div style="margin-bottom: 8px; font-size: 12px;">
                                            <a href="<?= BASE_URL ?>/public/uploads/shipping/<?= htmlspecialchars($order['shipping_attachment']) ?>" target="_blank" style="color: var(--color-primary); font-weight: 600;">View Current Attachment</a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="shipping_attachment" style="width: 100%; font-size: 13px;">
                                </div>
                                
                                <script>
                                    function toggleShippingFields() {
                                        const status = document.getElementById('status').value;
                                        document.getElementById('shippingFields').style.display = (status === 'Shipped') ? 'block' : 'none';
                                    }
                                </script>

                                <button type="submit" style="width: 100%; padding: 10px; background-color: var(--color-accent); color: #fff; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em; font-size: 12px;">Update Statuses</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>
