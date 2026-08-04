<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Customer Orders</h1>
                    <p style="color: #718096; font-size: 14px;">Manage all incoming customer orders for Dar Jana Fashion</p>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div style="margin-bottom: 50px;">
                <div class="table-responsive">
                    <?php if (empty($orders)): ?>
                        <p style="color: #718096; text-align: center; padding: 20px;">No customer orders placed yet.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ORDER #</th>
                                    <th>CUSTOMER</th>
                                    <th>PHONE</th>
                                    <th>ADDRESS</th>
                                    <th>TOTAL</th>
                                    <th>STATUS</th>
                                    <th>DATE</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $ord): ?>
                                    <tr>
                                        <td style="font-weight: 700; color: var(--color-accent);"><?= htmlspecialchars($ord['order_number']) ?></td>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                            <div style="font-size: 12px; color: #718096;"><?= htmlspecialchars($ord['email']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($ord['phone']) ?></td>
                                        <td><?= htmlspecialchars($ord['address']) ?>, <?= htmlspecialchars($ord['city']) ?></td>
                                        <td style="font-weight: 700;"><?= number_format($ord['total_amount'], 2) ?> BHD</td>
                                        <td>
                                            <span style="background: #fef3c7; color: #92400e; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 4px;"><?= $ord['status'] ?></span>
                                        </td>
                                        <td style="font-size: 12px; color: #718096;"><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/admin/order/delete/<?= $ord['id'] ?>" onclick="return confirm('Delete this order?')" style="color: #e53e3e; font-size: 12px; font-weight: 600;">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
</body>
</html>
