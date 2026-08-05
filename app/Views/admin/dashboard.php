<?php include __DIR__ . '/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h1 style="font-size: 26px;">Store Performance Dashboard</h1>
                    <p style="color: #718096; font-size: 14px;">Analytics overview for Dar Jana Fashion</p>
                </div>
                <div style="font-size: 13px; color: #718096;"><?= date('l, d F Y') ?></div>
            </div>

            <!-- ===== DATE RANGE FILTER BAR ===== -->
            <form method="GET" action="" id="dateFilterForm" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px 20px; margin-bottom:28px; display:flex; flex-wrap:wrap; align-items:center; gap:14px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <!-- Quick preset buttons -->
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <?php
                        $presets = [
                            'today'      => ['Today',       date('Y-m-d'),                          date('Y-m-d')],
                            'this_week'  => ['This Week',   date('Y-m-d', strtotime('monday this week')), date('Y-m-d')],
                            'this_month' => ['This Month',  date('Y-m-01'),                         date('Y-m-d')],
                            'last_month' => ['Last Month',  date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last month'))],
                            'this_year'  => ['This Year',   date('Y-01-01'),                        date('Y-m-d')],
                        ];
                    ?>
                    <?php foreach ($presets as $key => [$label, $ps, $pe]): ?>
                        <?php $isActive = ($startDate === $ps && $endDate === $pe); ?>
                        <button type="button" onclick="setPreset('<?= $ps ?>','<?= $pe ?>')" style="
                            padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; cursor:pointer;
                            border:1.5px solid <?= $isActive ? '#c5a059' : '#e2e8f0' ?>;
                            background:<?= $isActive ? '#c5a059' : '#fff' ?>;
                            color:<?= $isActive ? '#fff' : '#4a5568' ?>;
                            transition:all 0.15s;
                        "><?= $label ?></button>
                    <?php endforeach; ?>
                    <?php if (!$startDate && !$endDate): ?>
                        <button type="button" style="padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; border:1.5px solid #c5a059; background:#c5a059; color:#fff; cursor:default;">All Time</button>
                    <?php else: ?>
                        <button type="button" onclick="clearFilter()" style="padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; border:1.5px solid #e2e8f0; background:#fff; color:#4a5568; cursor:pointer;">All Time</button>
                    <?php endif; ?>
                </div>

                <!-- Divider -->
                <div style="width:1px; height:30px; background:#e2e8f0;"></div>

                <!-- Custom date inputs -->
                <div style="display:flex; align-items:center; gap:10px;">
                    <label style="font-size:12px; font-weight:600; color:#718096;">FROM</label>
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" style="padding:7px 10px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#181818;">
                    <label style="font-size:12px; font-weight:600; color:#718096;">TO</label>
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" style="padding:7px 10px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#181818;">
                    <button type="submit" style="padding:7px 20px; background:#181818; color:#fff; border:none; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Apply</button>
                </div>

                <?php if ($startDate && $endDate): ?>
                    <div style="font-size:12px; color:#c5a059; font-weight:600; margin-left:auto;">
                        Showing: <?= date('d M Y', strtotime($startDate)) ?> – <?= date('d M Y', strtotime($endDate)) ?>
                    </div>
                <?php endif; ?>
            </form>

            <script>
            function setPreset(start, end) {
                document.getElementById('start_date').value = start;
                document.getElementById('end_date').value = end;
                document.getElementById('dateFilterForm').submit();
            }
            function clearFilter() {
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
                document.getElementById('dateFilterForm').submit();
            }
            </script>

            <!-- KPI Cards Row -->
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 32px;">
                <div class="stat-card" style="border-left: 4px solid #c5a059;">
                    <div style="font-size: 11px; font-family: var(--heading-font-family); color: #718096; letter-spacing: 0.1em; margin-bottom: 6px;">TOTAL REVENUE</div>
                    <div style="font-size: 24px; font-weight: 700; color: #c5a059;"><?= $totalRevenue ?> <span style="font-size:13px;">BHD</span></div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #22c55e;">
                    <div style="font-size: 11px; font-family: var(--heading-font-family); color: #718096; letter-spacing: 0.1em; margin-bottom: 6px;">PAID REVENUE</div>
                    <div style="font-size: 24px; font-weight: 700; color: #22c55e;"><?= $paidRevenue ?> <span style="font-size:13px;">BHD</span></div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #181818;">
                    <div style="font-size: 11px; font-family: var(--heading-font-family); color: #718096; letter-spacing: 0.1em; margin-bottom: 6px;">TOTAL ORDERS</div>
                    <div style="font-size: 24px; font-weight: 700; color: #181818;"><?= $totalOrdersCount ?></div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #f97316;">
                    <div style="font-size: 11px; font-family: var(--heading-font-family); color: #718096; letter-spacing: 0.1em; margin-bottom: 6px;">PENDING ORDERS</div>
                    <div style="font-size: 24px; font-weight: 700; color: #f97316;"><?= $pendingCount ?></div>
                </div>
                <div class="stat-card" style="border-left: 4px solid #3b82f6;">
                    <div style="font-size: 11px; font-family: var(--heading-font-family); color: #718096; letter-spacing: 0.1em; margin-bottom: 6px;">PRODUCTS IN CATALOG</div>
                    <div style="font-size: 24px; font-weight: 700; color: #181818;"><?= $totalProductsCount ?></div>
                </div>
            </div>

            <!-- Order Status Badges Row -->
            <div style="display: flex; gap: 16px; margin-bottom: 32px; flex-wrap: wrap;">
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 12px 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 10px; height: 10px; background: #22c55e; border-radius: 50%; display: inline-block;"></span>
                    <span style="font-size: 13px; font-weight: 600;">Paid: <strong style="color:#22c55e;"><?= $paidCount ?></strong></span>
                </div>
                <div style="background: #fff7ed; border: 1px solid #fed7aa; padding: 12px 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 10px; height: 10px; background: #f97316; border-radius: 50%; display: inline-block;"></span>
                    <span style="font-size: 13px; font-weight: 600;">Pending: <strong style="color:#f97316;"><?= $pendingCount ?></strong></span>
                </div>
                <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 12px 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 10px; height: 10px; background: #ef4444; border-radius: 50%; display: inline-block;"></span>
                    <span style="font-size: 13px; font-weight: 600;">Failed: <strong style="color:#ef4444;"><?= $failedCount ?></strong></span>
                </div>
                <a href="<?= BASE_URL ?>/admin/orders" style="background: #181818; color: #fff; padding: 12px 22px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-left: auto;">
                    View All Orders →
                </a>
            </div>

            <!-- Charts Row -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 32px;">

                <!-- Revenue Chart -->
                <div style="background: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                    <div style="font-size: 14px; font-weight: 700; color: #181818; margin-bottom: 18px; font-family: var(--heading-font-family); letter-spacing: 0.05em;">REVENUE – LAST 6 MONTHS (BHD)</div>
                    <canvas id="revenueChart" height="120"></canvas>
                </div>

                <!-- Order Status Doughnut -->
                <div style="background: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div style="font-size: 14px; font-weight: 700; color: #181818; margin-bottom: 18px; font-family: var(--heading-font-family); letter-spacing: 0.05em; align-self: flex-start;">ORDER STATUS BREAKDOWN</div>
                    <canvas id="statusChart" style="max-width: 200px; max-height: 200px;"></canvas>
                    <div style="margin-top: 16px; display: flex; gap: 14px; flex-wrap: wrap; justify-content: center;">
                        <span style="font-size: 12px; font-weight: 600; color: #22c55e;">● Paid (<?= $paidCount ?>)</span>
                        <span style="font-size: 12px; font-weight: 600; color: #f97316;">● Pending (<?= $pendingCount ?>)</span>
                        <span style="font-size: 12px; font-weight: 600; color: #ef4444;">● Failed (<?= $failedCount ?>)</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Top Products + Recent Orders -->
            <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: 24px;">

                <!-- Top 5 Products -->
                <div style="background: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                    <div style="font-size: 14px; font-weight: 700; color: #181818; margin-bottom: 16px; font-family: var(--heading-font-family); letter-spacing: 0.05em;">TOP SELLING PRODUCTS</div>
                    <?php if (!empty($topProducts)): ?>
                        <?php foreach ($topProducts as $i => $tp): ?>
                            <div style="display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; <?= $i === count($topProducts) - 1 ? 'border-bottom:none;' : '' ?>">
                                <div style="width: 28px; height: 28px; background: <?= ['#c5a059','#181818','#3b82f6','#22c55e','#f97316'][$i] ?>; color:#fff; border-radius: 50%; display:flex; align-items:center; justify-content:center; font-size: 12px; font-weight: 700; flex-shrink: 0;"><?= $i + 1 ?></div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($tp['product_name']) ?></div>
                                    <div style="font-size: 11px; color: #718096;"><?= (int)$tp['total_qty'] ?> units · <?= number_format((float)$tp['total_revenue'], 3) ?> BHD</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="color: #718096; font-size: 13px; text-align: center; padding: 30px 0;">No order data yet.</div>
                    <?php endif; ?>
                </div>

                <!-- Recent Orders -->
                <div style="background: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div style="font-size: 14px; font-weight: 700; color: #181818; font-family: var(--heading-font-family); letter-spacing: 0.05em;">RECENT ORDERS</div>
                        <a href="<?= BASE_URL ?>/admin/orders" style="font-size: 12px; color: #c5a059; font-weight: 600; text-decoration: none;">View All →</a>
                    </div>
                    <table style="width:100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="color: #718096;">
                                <th style="text-align:left; padding: 6px 10px; font-size: 11px; font-weight: 600; letter-spacing:0.05em; border-bottom: 1px solid #e2e8f0;">ORDER #</th>
                                <th style="text-align:left; padding: 6px 10px; font-size: 11px; font-weight: 600; letter-spacing:0.05em; border-bottom: 1px solid #e2e8f0;">CUSTOMER</th>
                                <th style="text-align:left; padding: 6px 10px; font-size: 11px; font-weight: 600; letter-spacing:0.05em; border-bottom: 1px solid #e2e8f0;">AMOUNT</th>
                                <th style="text-align:left; padding: 6px 10px; font-size: 11px; font-weight: 600; letter-spacing:0.05em; border-bottom: 1px solid #e2e8f0;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $ro): ?>
                                <?php
                                    $st = strtolower($ro['payment_status'] ?? 'pending');
                                    $badgeColor = $st === 'paid' ? '#22c55e' : ($st === 'failed' ? '#ef4444' : '#f97316');
                                    $statusLabel = ucfirst($ro['payment_status'] ?? 'Pending');
                                ?>
                                <tr>
                                    <td style="padding: 10px 10px; border-bottom: 1px solid #f1f5f9;">
                                        <a href="<?= BASE_URL ?>/admin/order/<?= $ro['id'] ?>" style="color:#181818; font-weight:600; text-decoration:none;"><?= htmlspecialchars($ro['order_number']) ?></a>
                                    </td>
                                    <td style="padding: 10px 10px; border-bottom: 1px solid #f1f5f9;"><?= htmlspecialchars($ro['customer_name']) ?></td>
                                    <td style="padding: 10px 10px; border-bottom: 1px solid #f1f5f9; font-weight:600;"><?= number_format((float)$ro['total_amount'], 3) ?> BHD</td>
                                    <td style="padding: 10px 10px; border-bottom: 1px solid #f1f5f9;">
                                        <span style="background: <?= $badgeColor ?>22; color: <?= $badgeColor ?>; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;"><?= $statusLabel ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentOrders)): ?>
                                <tr><td colspan="4" style="text-align: center; padding: 20px; color: #718096;">No orders yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

<script>
// --- Revenue Line Chart ---
const revenueLabels = <?= json_encode($chartLabels) ?>;
const revenueData   = <?= json_encode($chartData) ?>;

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Revenue (BHD)',
            data: revenueData,
            backgroundColor: 'rgba(197,160,89,0.18)',
            borderColor: '#c5a059',
            borderWidth: 2,
            borderRadius: 6,
            hoverBackgroundColor: 'rgba(197,160,89,0.35)',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { callback: v => v + ' BHD' } },
            x: { grid: { display: false } }
        }
    }
});

// --- Status Doughnut Chart ---
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Paid', 'Pending', 'Failed'],
        datasets: [{
            data: [<?= $paidCount ?>, <?= $pendingCount ?>, <?= $failedCount ?>],
            backgroundColor: ['#22c55e', '#f97316', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        cutout: '70%',
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
</script>
</body>
</html>
