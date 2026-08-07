<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($subject ?? 'Exclusive Showcase - Dar Jana Fashion') ?></title>
    <meta name="author" content="SaNDS Lab - www.sandslab.com">
    <meta name="developer" content="Developed by SaNDS Lab (www.sandslab.com)">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f5f7; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #181818; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f5f7; padding: 30px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); max-width: 600px; width: 100%;">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #181818; padding: 30px 20px;">
                            <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>/assets/images/web_logo_menu.png" alt="Dar Jana Fashion" style="height: 48px; width: auto; display: block; border: 0;">
                            <div style="color: #c5a059; font-size: 11px; letter-spacing: 0.25em; text-transform: uppercase; margin-top: 8px; font-weight: 600;">LUXURY ABAYAS &amp; MODEST COUTURE</div>
                        </td>
                    </tr>

                    <!-- Body Intro / Custom Message -->
                    <tr>
                        <td style="padding: 32px 32px 16px;">
                            <?php if (!empty($customMessage)): ?>
                                <div style="background: #fdfbf7; border-left: 4px solid #c5a059; padding: 18px 22px; border-radius: 4px; margin-bottom: 24px; font-size: 14px; line-height: 1.6; color: #4a5568;">
                                    <?= nl2br(htmlspecialchars($customMessage)) ?>
                                </div>
                            <?php else: ?>
                                <h2 style="font-size: 22px; font-weight: 700; color: #181818; margin: 0 0 10px; text-align: center;">Curated Luxury Selection</h2>
                                <p style="font-size: 14px; color: #718096; margin: 0 0 24px; text-align: center; line-height: 1.5;">We are delighted to present an exclusive spotlight from our latest collection for our valued VIP subscribers.</p>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Featured Product Card -->
                    <tr>
                        <td style="padding: 0 32px 32px;">
                            <?php 
                                $prodImage = $product['image'] ?? '';
                                if ($prodImage && strpos($prodImage, 'http') !== 0) {
                                    $prodImage = (defined('BASE_URL') ? BASE_URL : '') . '/' . ltrim($prodImage, '/');
                                }
                                $subToken = !empty($recipientEmail) ? '&sub=' . urlencode($recipientEmail) : '';
                                $prodUrl = (defined('BASE_URL') ? BASE_URL : '') . '/product/' . htmlspecialchars($product['slug'] ?? '') . '?source=email' . $subToken;
                            ?>
                            <div style="border: 1px solid #edf2f7; border-radius: 10px; overflow: hidden; background: #ffffff;">
                                <a href="<?= $prodUrl ?>" target="_blank" style="text-decoration: none; display: block;">
                                    <img src="<?= htmlspecialchars($prodImage) ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>" style="width: 100%; max-height: 380px; object-fit: cover; display: block; border: 0;">
                                </a>
                                <div style="padding: 24px; text-align: center;">
                                    <div style="font-size: 11px; font-weight: 700; color: #c5a059; letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 4px;">
                                        CODE: <?= htmlspecialchars($product['product_code'] ?? '') ?>
                                    </div>
                                    <h3 style="font-size: 20px; font-weight: 700; color: #181818; margin: 0 0 12px;">
                                        <?= htmlspecialchars($product['name'] ?? '') ?>
                                    </h3>
                                    
                                    <!-- Price Display -->
                                    <div style="margin-bottom: 20px;">
                                        <?php if (!empty($product['sale_price']) && (float)$product['sale_price'] > 0 && (float)$product['sale_price'] < (float)$product['price']): ?>
                                            <span style="font-size: 14px; text-decoration: line-through; color: #a0aec0; margin-right: 8px;">
                                                <?= number_format((float)$product['price'], 2) ?> BHD
                                            </span>
                                            <span style="font-size: 22px; font-weight: 800; color: #e53e3e;">
                                                <?= number_format((float)$product['sale_price'], 2) ?> BHD
                                            </span>
                                        <?php else: ?>
                                            <span style="font-size: 22px; font-weight: 800; color: #181818;">
                                                <?= number_format((float)$product['price'], 2) ?> BHD
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- CTA Button -->
                                    <a href="<?= $prodUrl ?>" target="_blank" style="display: inline-block; background-color: #181818; color: #ffffff; padding: 14px 32px; border-radius: 6px; font-size: 13px; font-weight: 700; text-decoration: none; letter-spacing: 0.15em; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                        EXPLORE &amp; ORDER NOW →
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #faf8f5; border-top: 1px solid #edf2f7; padding: 24px 32px; text-align: center; font-size: 12px; color: #718096; line-height: 1.6;">
                            <div style="font-size: 13px; font-weight: 700; color: #181818; margin-bottom: 4px;">DAR JANA FASHION</div>
                            <div>Express GCC Delivery to Bahrain, Kuwait, KSA, UAE, Qatar &amp; Oman</div>
                            <div style="margin-top: 8px;">Customer Support: +973 3330 0160 | info@darjanafashion.com</div>
                            <div style="margin-top: 12px; font-size: 11px; color: #a0aec0;">
                                You are receiving this VIP update because you subscribed at <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>" style="color: #c5a059; text-decoration: none;">www.darjanafashion.com</a>.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
