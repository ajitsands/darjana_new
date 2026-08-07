<?php include __DIR__ . '/header.php'; ?>
<div class="admin-main">
    <!-- Header with Back Button and Quick Info -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <a href="<?= BASE_URL ?>/admin/products" style="color: #718096; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    &larr; Back to Products
                </a>
            </div>
            <h1 style="font-size: 26px; margin: 0;">Click Insights & Performance</h1>
            <p style="color: #718096; font-size: 14px; margin-top: 4px;">Real-time engagement tracking, social channel metrics &amp; product click performance</p>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="background: #edf2f7; color: #4a5568; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                ⏱️ Window: <?= htmlspecialchars($settings['share_click_dedup_minutes'] ?? '60') ?> Mins Deduplicated
            </span>
            <a href="<?= BASE_URL ?>/admin/settings" style="color: #2b6cb0; font-size: 12px; font-weight: 600; text-decoration: underline;">Configure Window</a>
        </div>
    </div>

    <!-- SUMMARY METRIC CARDS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Total Clicks -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid #2b6cb0;">
            <div style="font-size: 12px; color: #718096; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">TOTAL SHARE CLICKS</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a202c; margin-top: 6px;"><?= number_format($insights['summary']['total_clicks']) ?></div>
            <div style="font-size: 11px; color: #38a169; margin-top: 4px;">Tracked across all platforms</div>
        </div>

        <!-- Top Product -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid #c5a059;">
            <div style="font-size: 12px; color: #718096; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">MOST CLICKED PRODUCT</div>
            <div style="font-size: 16px; font-weight: 700; color: #1a202c; margin-top: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($insights['summary']['top_product']) ?>">
                <?= htmlspecialchars($insights['summary']['top_product']) ?>
            </div>
            <div style="font-size: 11px; color: #718096; margin-top: 4px;">Highest customer interest</div>
        </div>

        <!-- Top Social Channel -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid #38a169;">
            <div style="font-size: 12px; color: #718096; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">TOP SOCIAL CHANNEL</div>
            <div style="font-size: 22px; font-weight: 700; color: #1a202c; margin-top: 6px;">
                <?= htmlspecialchars($insights['summary']['top_platform']) ?>
            </div>
            <div style="font-size: 11px; color: #718096; margin-top: 4px;">Primary traffic generator</div>
        </div>

        <!-- Top Territory -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-left: 4px solid #805ad5;">
            <div style="font-size: 12px; color: #718096; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">TOP LOCATION</div>
            <div style="font-size: 20px; font-weight: 700; color: #1a202c; margin-top: 6px;">
                🌍 <?= htmlspecialchars($insights['summary']['top_country']) ?>
            </div>
            <div style="font-size: 11px; color: #718096; margin-top: 4px;">Top origin of clicks</div>
        </div>
    </div>

    <!-- TAB NAVIGATION BAR -->
    <div style="border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" class="tab-nav-btn active" onclick="switchInsightTab('productTab', this)">
            📦 Product Performance by Click
        </button>
        <button type="button" class="tab-nav-btn" onclick="switchInsightTab('socialTab', this)">
            📱 Click Performance via Social Medias
        </button>
        <button type="button" class="tab-nav-btn" onclick="switchInsightTab('locationTab', this)">
            🌍 Geographic Locations &amp; Live Stream
        </button>
    </div>

    <style>
        .tab-nav-btn {
            background: none;
            border: none;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #718096;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
            margin-bottom: -2px;
        }
        .tab-nav-btn:hover { color: #2b6cb0; }
        .tab-nav-btn.active { color: #2b6cb0; border-bottom-color: #2b6cb0; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
    </style>

    <!-- DataTables & Chart.js Dependencies -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- TAB 1: PRODUCT PERFORMANCE BY CLICK -->
    <div id="productTab" class="tab-pane active">
        <!-- Graphical Report -->
        <div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 30px;">
            <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 20px; color: #2d3748; display: flex; align-items: center; gap: 8px;">
                📊 Graphical Report: Product Clicks Distribution
            </h3>
            <div style="height: 280px; position: relative;">
                <canvas id="productClicksChart"></canvas>
            </div>
        </div>

        <!-- DataTable Report -->
        <div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 20px; color: #2d3748; display: flex; align-items: center; gap: 8px;">
                📋 DataTable Report: Product Click Performance
            </h3>
            <div class="table-responsive">
                <table id="productPerformanceTable" class="display" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>PRODUCT IMAGE</th>
                            <th>CODE &amp; NAME</th>
                            <th>CATEGORY</th>
                            <th>PRICE</th>
                            <th>TOTAL CLICKS</th>
                            <th>SOCIAL PLATFORMS</th>
                            <th>TOP LOCATION</th>
                            <th>LAST CLICKED</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($insights['product_performance'] as $prod): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars(str_replace('/uploads/products/high/', '/uploads/products/tiny/', $prod['image'])) ?>" style="width: 44px; height: 44px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>
                                    <div style="font-size: 11px; color: #c5a059; font-weight: 700;"><?= htmlspecialchars($prod['product_code']) ?></div>
                                    <div style="font-weight: 600; font-size: 13px; color: #1a202c;"><?= htmlspecialchars($prod['name']) ?></div>
                                </td>
                                <td><span style="font-size: 12px; color: #4a5568;"><?= htmlspecialchars($prod['category_name']) ?></span></td>
                                <td><span style="font-weight: 700; font-size: 13px;"><?= number_format($prod['price'], 2) ?> BHD</span></td>
                                <td>
                                    <span style="font-weight: 700; font-size: 14px; color: #2b6cb0; background: #ebf8ff; padding: 4px 10px; border-radius: 12px; border: 1px solid #bee3f8;">
                                        <?= $prod['total_clicks'] ?> Clicks
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                        <?php 
                                            $srcs = $prod['by_source'];
                                            if (($srcs['whatsapp'] ?? 0) > 0) echo '<span style="background: #f0fff4; color: #38a169; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">💬 WA: ' . $srcs['whatsapp'] . '</span>';
                                            if (($srcs['instagram'] ?? 0) > 0) echo '<span style="background: #fefcbf; color: #d69e2e; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">📸 IG: ' . $srcs['instagram'] . '</span>';
                                            if (($srcs['facebook'] ?? 0) > 0) echo '<span style="background: #ebf8ff; color: #3182ce; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">📘 FB: ' . $srcs['facebook'] . '</span>';
                                            if (($srcs['tiktok'] ?? 0) > 0) echo '<span style="background: #faf5ff; color: #805ad5; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">🎵 TT: ' . $srcs['tiktok'] . '</span>';
                                            if (($srcs['youtube'] ?? 0) > 0) echo '<span style="background: #fff5f5; color: #e53e3e; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">📺 YT: ' . $srcs['youtube'] . '</span>';
                                            $emailCount = ($srcs['email'] ?? 0) + ($srcs['email_campaign'] ?? 0);
                                            if ($emailCount > 0) echo '<span style="background: #f3e8ff; color: #7c3aed; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">✉️ Email: ' . $emailCount . '</span>';
                                            if (array_sum($srcs) == 0) echo '<span style="color: #a0aec0; font-size: 11px;">No clicks</span>';
                                        ?>
                                    </div>
                                </td>
                                <td><span style="font-size: 12px; font-weight: 600; color: #4a5568;"><?= htmlspecialchars($prod['top_location']) ?></span></td>
                                <td>
                                    <span style="font-size: 11px; color: #718096;">
                                        <?= $prod['last_clicked_at'] ? date('M d, Y - h:i A', strtotime($prod['last_clicked_at'])) : 'Never' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: CLICK PERFORMANCE VIA SOCIAL MEDIAS -->
    <div id="socialTab" class="tab-pane">
        <!-- Graphical Reports Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <!-- Doughnut Chart: Traffic Share % -->
            <div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 20px; color: #2d3748;">
                    🍰 Social Media Traffic Share (%)
                </h3>
                <div style="height: 250px; position: relative;">
                    <canvas id="socialShareDoughnutChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Platform Clicks Comparison -->
            <div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 20px; color: #2d3748;">
                    📊 Clicks per Social Platform
                </h3>
                <div style="height: 250px; position: relative;">
                    <canvas id="socialClicksBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- DataTable Report -->
        <div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 20px; color: #2d3748;">
                📋 DataTable Report: Social Media Click Metrics
            </h3>
            <div class="table-responsive">
                <table id="socialPerformanceTable" class="display" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>SOCIAL PLATFORM</th>
                            <th>TOTAL CLICKS</th>
                            <th>TRAFFIC SHARE %</th>
                            <th>TOP CLICKED PRODUCT</th>
                            <th>TOP VISITOR COUNTRY</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($insights['platform_performance'] as $plat): ?>
                            <tr>
                                <td style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 14px;">
                                    <span style="font-size: 22px;"><?= $plat['icon'] ?></span>
                                    <span><?= htmlspecialchars($plat['name']) ?></span>
                                </td>
                                <td>
                                    <span style="font-weight: 700; font-size: 14px; color: <?= $plat['color'] ?>;">
                                        <?= number_format($plat['total_clicks']) ?> Clicks
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 100px; background: #edf2f7; height: 8px; border-radius: 4px; overflow: hidden;">
                                            <div style="width: <?= $plat['share_percent'] ?>%; background: <?= $plat['color'] ?>; height: 100%;"></div>
                                        </div>
                                        <span style="font-weight: 700; font-size: 12px;"><?= $plat['share_percent'] ?>%</span>
                                    </div>
                                </td>
                                <td><span style="font-weight: 600; font-size: 13px; color: #2d3748;"><?= htmlspecialchars($plat['top_product']) ?></span></td>
                                <td><span style="font-weight: 600; font-size: 13px; color: #2d3748;">🌍 <?= htmlspecialchars($plat['top_country']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: GEOGRAPHIC LOCATIONS & RECENT CLICK STREAM -->
    <div id="locationTab" class="tab-pane">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <!-- Geographic Locations Card -->
            <div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 16px; color: #2d3748;">
                    🌍 Geographic Location Breakdown
                </h3>
                <?php if (empty($insights['location_performance'])): ?>
                    <p style="color: #a0aec0; font-size: 13px; font-style: italic;">No geographic click data recorded yet.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 10px; max-height: 360px; overflow-y: auto;">
                        <?php foreach ($insights['location_performance'] as $loc): ?>
                            <?php 
                                $code = strtoupper($loc['country_code'] ?? 'UN');
                                $flag = '📍';
                                if ($code === 'LOCAL') $flag = '📍';
                                else if (strlen($code) === 2) {
                                    $flag = mb_chr(127397 + ord($code[0])) . mb_chr(127397 + ord($code[1]));
                                }
                            ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f7fafc; border-radius: 6px; border: 1px solid #edf2f7;">
                                <div style="font-weight: 600; font-size: 13px;">
                                    <span><?= $flag ?></span> <?= htmlspecialchars($loc['country'] ?: 'Unknown') ?>
                                    <span style="font-weight: 400; color: #718096; font-size: 12px;">(<?= htmlspecialchars($loc['city'] ?: 'Unknown') ?>)</span>
                                </div>
                                <span style="font-weight: 700; color: #2b6cb0; background: #ebf8ff; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                                    <?= $loc['total_clicks'] ?> <?= $loc['total_clicks'] == 1 ? 'click' : 'clicks' ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Click Activity Stream -->
            <div style="background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 16px; color: #2d3748;">
                    ⚡ Live Recent Clicks Stream (Latest 25)
                </h3>
                <?php if (empty($insights['recent_clicks'])): ?>
                    <p style="color: #a0aec0; font-size: 13px; font-style: italic;">No click logs recorded yet.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 8px; max-height: 360px; overflow-y: auto;">
                        <?php foreach ($insights['recent_clicks'] as $log): ?>
                            <?php 
                                $srcIcon = '🔗';
                                if ($log['source'] === 'whatsapp') $srcIcon = '💬';
                                else if ($log['source'] === 'instagram') $srcIcon = '📸';
                                else if ($log['source'] === 'facebook') $srcIcon = '📘';
                                else if ($log['source'] === 'tiktok') $srcIcon = '🎵';
                                else if ($log['source'] === 'youtube') $srcIcon = '📺';
                            ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px;">
                                <div>
                                    <span style="margin-right: 6px;"><?= $srcIcon ?></span>
                                    <strong style="color: #2d3748;"><?= htmlspecialchars($log['product_code']) ?></strong> - 
                                    <span style="color: #4a5568;"><?= htmlspecialchars($log['product_name']) ?></span>
                                </div>
                                <div style="text-align: right; color: #718096; font-size: 11px;">
                                    <div><?= htmlspecialchars($log['country']) ?> (<?= htmlspecialchars($log['city']) ?>)</div>
                                    <div><?= date('M d, h:i A', strtotime($log['clicked_at'])) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function switchInsightTab(tabId, btn) {
    document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-nav-btn').forEach(el => el.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');
    btn.classList.add('active');
}

$(document).ready(function() {
    $('#productPerformanceTable').DataTable({
        "pageLength": 10,
        "order": [[4, "desc"]],
        "language": { "search": "Search Product Clicks:" }
    });

    $('#socialPerformanceTable').DataTable({
        "paging": false,
        "info": false,
        "order": [[1, "desc"]],
        "language": { "search": "Search Platforms:" }
    });

    // 1. Chart.js: Product Clicks Bar Chart
    const prodLabels = <?= json_encode(array_column(array_slice($insights['product_performance'], 0, 10), 'name')) ?>;
    const prodData = <?= json_encode(array_column(array_slice($insights['product_performance'], 0, 10), 'total_clicks')) ?>;

    const ctxProduct = document.getElementById('productClicksChart').getContext('2d');
    new Chart(ctxProduct, {
        type: 'bar',
        data: {
            labels: prodLabels,
            datasets: [{
                label: 'Total Clicks',
                data: prodData,
                backgroundColor: '#2b6cb0',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { ticks: { font: { size: 11 } } }
            }
        }
    });

    // 2. Chart.js: Social Share Doughnut Chart
    const socialLabels = <?= json_encode(array_column($insights['platform_performance'], 'name')) ?>;
    const socialData = <?= json_encode(array_column($insights['platform_performance'], 'total_clicks')) ?>;
    const socialColors = <?= json_encode(array_column($insights['platform_performance'], 'color')) ?>;

    const ctxSocialDoughnut = document.getElementById('socialShareDoughnutChart').getContext('2d');
    new Chart(ctxSocialDoughnut, {
        type: 'doughnut',
        data: {
            labels: socialLabels,
            datasets: [{
                data: socialData,
                backgroundColor: socialColors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right' } }
        }
    });

    // 3. Chart.js: Social Clicks Bar Chart
    const ctxSocialBar = document.getElementById('socialClicksBarChart').getContext('2d');
    new Chart(ctxSocialBar, {
        type: 'bar',
        data: {
            labels: socialLabels,
            datasets: [{
                label: 'Clicks',
                data: socialData,
                backgroundColor: socialColors,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
});
</script>
</body>
</html>
