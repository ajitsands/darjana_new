<?php include __DIR__ . '/header.php'; ?>

<?php
function getViewFlagEmoji($countryCode) {
    if (!$countryCode || $countryCode === 'LOCAL') return '📍';
    if ($countryCode === 'UN') return '🌐';
    try {
        $codePoints = array_map(fn($char) => 127397 + ord($char), str_split(strtoupper($countryCode)));
        return mb_chr($codePoints[0], 'UTF-8') . mb_chr($codePoints[1], 'UTF-8');
    } catch (Exception $e) {
        return '🌐';
    }
}
?>

<div class="admin-main" style="padding: 28px; background: #f8fafc; min-height: 100vh;">
    <!-- Top Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="<?= BASE_URL ?>/admin" style="color: #64748b; font-size: 13px; text-decoration: none; font-weight: 600;">← Dashboard</a>
                <span style="color: #cbd5e0;">/</span>
                <span style="color: #8b5cf6; font-size: 13px; font-weight: 700;">Product View Insights</span>
            </div>
            <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin-top: 6px;">Product Detail Views &amp; Location Analytics</h1>
            <p style="color: #64748b; font-size: 13px; margin-top: 2px;">Track product clicks, repeat views per IP address, and geographic locations</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= BASE_URL ?>/admin/products" style="background: #ffffff; border: 1px solid #cbd5e0; color: #334155; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none;">📦 Product Catalog</a>
            <a href="<?= BASE_URL ?>/admin/click-insights" style="background: #2563eb; color: #ffffff; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none;">📤 Share Click Insights</a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">TOTAL PRODUCT VIEWS</div>
            <div style="font-size: 28px; font-weight: 800; color: #8b5cf6; margin-top: 6px;"><?= number_format($summary['total_views']) ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Detail page views recorded</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">UNIQUE VISITOR IPS</div>
            <div style="font-size: 28px; font-weight: 800; color: #0284c7; margin-top: 6px;"><?= number_format($summary['unique_ips']) ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Distinct IP addresses tracked</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">TOP VIEWED PRODUCT</div>
            <div style="font-size: 16px; font-weight: 700; color: #1e293b; margin-top: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($summary['top_product']) ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Most clicked dress/abaya</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">TOP VIEWING COUNTRY</div>
            <div style="font-size: 18px; font-weight: 700; color: #16a34a; margin-top: 8px;"><?= htmlspecialchars($summary['top_country']) ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Highest location traffic</div>
        </div>
    </div>

    <!-- Section 1: Repeat Visitor IP Breakdown Report -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; margin-bottom: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">🔍 Repeat Visitor IP Report (Multi-View Frequency)</h3>
                <p style="font-size: 12.5px; color: #64748b; margin-top: 2px;">Shows how many times the same user/IP viewed the same product item</p>
            </div>
            <span style="background: #f3e8ff; color: #7c3aed; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px;">IP Tracking Active</span>
        </div>

        <?php if (!empty($repeatIpReport)): ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                    <thead>
                        <tr style="background: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0;">Product</th>
                            <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0;">Visitor IP Address</th>
                            <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0;">Location</th>
                            <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; text-align: center;">Times Viewed from Same IP</th>
                            <th style="padding: 10px 14px; border-bottom: 1px solid #e2e8f0; text-align: right;">Latest View Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repeatIpReport as $idx => $r): ?>
                            <?php $tinyImg = str_replace('/uploads/products/high/', '/uploads/products/tiny/', $r['image']); ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; <?= $r['ip_view_count'] > 1 ? 'background: #faf5ff;' : '' ?>">
                                <td style="padding: 10px 14px; display: flex; align-items: center; gap: 10px;">
                                    <img src="<?= htmlspecialchars($tinyImg) ?>" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <div style="font-size: 11px; color: #c5a059; font-weight: 700;"><?= htmlspecialchars($r['product_code']) ?></div>
                                        <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($r['product_name']) ?></div>
                                    </div>
                                </td>
                                <td style="padding: 10px 14px; font-family: monospace; font-size: 12.5px; color: #334155; font-weight: 600;">
                                    <?= htmlspecialchars($r['ip_address']) ?>
                                </td>
                                <td style="padding: 10px 14px;">
                                    <span style="font-size: 14px; margin-right: 4px;"><?= getViewFlagEmoji($r['country_code']) ?></span>
                                    <strong style="color: #334155;"><?= htmlspecialchars($r['country']) ?></strong>
                                    <span style="color: #64748b; font-size: 11.5px;">(<?= htmlspecialchars($r['city']) ?>)</span>
                                </td>
                                <td style="padding: 10px 14px; text-align: center;">
                                    <?php if ($r['ip_view_count'] > 1): ?>
                                        <span style="background: #f3e8ff; color: #7c3aed; border: 1px solid #d8b4fe; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                            🔥 <?= number_format($r['ip_view_count']) ?> Views
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 12px;">
                                            1 View
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 10px 14px; text-align: right; color: #64748b; font-size: 12px;">
                                    <?= date('d M Y, h:i A', strtotime($r['last_viewed_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic; font-size: 13px;">No repeat product view logs recorded yet.</div>
        <?php endif; ?>
    </div>

    <!-- Section 2 & 3: Most Viewed Ranking & Geolocation Distribution -->
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; margin-bottom: 28px;">
        <!-- Most Viewed Products Table -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 16px;">🏆 Product Views Ranking</h3>
            <?php if (!empty($productPerformance)): ?>
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase;">
                            <th style="padding: 8px 10px; text-align: left;">Rank</th>
                            <th style="padding: 8px 10px; text-align: left;">Product</th>
                            <th style="padding: 8px 10px; text-align: right;">Price</th>
                            <th style="padding: 8px 10px; text-align: right;">Total Views</th>
                            <th style="padding: 8px 10px; text-align: right;">Unique Visitors</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productPerformance as $idx => $p): ?>
                            <?php $tinyImg = str_replace('/uploads/products/high/', '/uploads/products/tiny/', $p['image']); ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px; font-weight: 700; color: #64748b; width: 40px; text-align: center;">
                                    #<?= $idx + 1 ?>
                                </td>
                                <td style="padding: 10px; display: flex; align-items: center; gap: 10px;">
                                    <img src="<?= htmlspecialchars($tinyImg) ?>" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <div style="font-size: 10.5px; color: #c5a059; font-weight: 700;"><?= htmlspecialchars($p['product_code']) ?></div>
                                        <a href="<?= BASE_URL ?>/product/<?= htmlspecialchars($p['slug']) ?>" target="_blank" style="font-weight: 600; color: #1e293b; text-decoration: none;"><?= htmlspecialchars($p['name']) ?></a>
                                    </div>
                                </td>
                                <td style="padding: 10px; text-align: right; font-weight: 600; color: #475569;">
                                    <?= number_format($p['price'], 2) ?> BHD
                                </td>
                                <td style="padding: 10px; text-align: right;">
                                    <span style="background: #f3e8ff; color: #7c3aed; font-weight: 700; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                                        👁️ <?= number_format($p['total_views']) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; text-align: right; font-weight: 600; color: #0284c7;">
                                    👤 <?= number_format($p['unique_visitors']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic;">No product view data yet.</div>
            <?php endif; ?>
        </div>

        <!-- Geolocation Location Breakdown -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 16px;">🌐 Geographic Location Breakdown</h3>
            <?php if (!empty($locationPerformance)): ?>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php 
                        $maxLoc = max(array_column($locationPerformance, 'total_views')) ?: 1;
                        foreach ($locationPerformance as $loc): 
                            $pct = round(($loc['total_views'] / $maxLoc) * 100);
                    ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 12.5px; margin-bottom: 4px;">
                                <span>
                                    <span style="font-size: 15px; margin-right: 4px;"><?= getViewFlagEmoji($loc['country_code']) ?></span>
                                    <strong style="color: #1e293b;"><?= htmlspecialchars($loc['country']) ?></strong>
                                    <span style="color: #64748b; font-size: 11px;">(<?= htmlspecialchars($loc['city']) ?>)</span>
                                </span>
                                <span style="font-weight: 700; color: #8b5cf6;"><?= number_format($loc['total_views']) ?> views</span>
                            </div>
                            <div style="width: 100%; height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden;">
                                <div style="width: <?= $pct ?>%; height: 100%; background: #8b5cf6; border-radius: 3px;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; color: #94a3b8; font-style: italic;">No location data available.</div>
            <?php endif; ?>
        </div>
</div>
</body>
</html>
