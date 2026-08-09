<?php
if (!isset($siteSettings)) {
    require_once __DIR__ . '/../../Models/Setting.php';
    $settingModelHelper = new Setting();
    $siteSettings = $settingModelHelper->getAll();
}
$homeHeroVideoUrl = !empty($siteSettings['home_hero_video']) 
    ? (BASE_URL . $siteSettings['home_hero_video']) 
    : (BASE_URL . '/assets/videos/home_video.mp4');
?>
<!-- Full Screen Clean Video Hero Banner (No poster image fallback) -->
<section class="hero-video-section">
    <video class="hero-video-element" autoplay loop muted playsinline preload="auto">
        <source src="<?= htmlspecialchars($homeHeroVideoUrl) ?>">
        Your browser does not support the video tag.
    </video>
</section>

<!-- Promotional Announcement Callout (Section Padding: 50px) -->
<?php
$promoTagline = $siteSettings['promo_tagline'] ?? 'PROMOTION';
$promoTitle = $siteSettings['promo_title'] ?? 'التوصيل مجاني لمدة أسبوع';
$promoDesc = $siteSettings['promo_desc'] ?? 'Enjoy complimentary express delivery on all dress and abaya orders across all GCC regions for a limited period.';
?>
<div style="background-color: #f3f3f3; border-bottom: 1px solid var(--color-border); padding: 50px 0; text-align: center;">
    <div class="container">
        <span style="font-family: var(--heading-font-family); font-size: 11px; letter-spacing: 0.25em; color: var(--color-accent); font-weight: 700; display: block; margin-bottom: 8px;"><?= htmlspecialchars($promoTagline) ?></span>
        <h3 style="font-size: 24px; margin: 4px 0 10px; font-weight: 600; color: var(--color-primary);"><?= htmlspecialchars($promoTitle) ?></h3>
        <p style="color: var(--color-text-muted); font-size: 15px; max-width: 720px; margin: 0 auto; line-height: 1.6;"><?= htmlspecialchars($promoDesc) ?></p>
    </div>
</div>

<!-- Featured Collection Products Block (Full Screen Width Slider) -->
<section style="padding: 50px 0;">
    <div class="full-width-container">
        <!-- Identical Section Header Format & Font Size -->
        <div class="section-title-wrap" style="margin-bottom: 36px;">
            <span style="font-family: var(--heading-font-family); font-size: 11px; letter-spacing: 0.25em; color: var(--color-accent); font-weight: 700; display: block; margin-bottom: 6px;">HANDPICKED COUTURE</span>
            <h2 class="section-title">FEATURED COLLECTION</h2>
        </div>

        <!-- Single Row Sliding Carousel Wrapper with Left & Right Arrow Navigation -->
        <div class="featured-slider-wrapper">
            <!-- Left Arrow Button -->
            <button class="slider-arrow-btn prev-btn" id="featuredPrevBtn" title="Slide Left" aria-label="Previous Product">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"></path></svg>
            </button>

            <!-- Single Row Horizontal Scroll Track -->
            <div class="featured-slider-track" id="featuredSliderTrack">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="featured-slider-item">
                        <div class="product-card">
                            <!-- Image with Corner '+' Box & Dynamic Admin Offer Tag (% or Amount) -->
                            <div class="product-image-wrap">
                                <?php if (!empty($product['sale_price']) && $product['sale_price'] > 0 && $product['price'] > $product['sale_price']): ?>
                                    <?php 
                                        $tagType = $product['offer_tag_type'] ?? 'percentage';
                                        if ($tagType === 'amount'):
                                            $saveAmtVal = $product['price'] - $product['sale_price'];
                                            if ($saveAmtVal > 0):
                                                $saveAmt = number_format($saveAmtVal, 3);
                                    ?>
                                                <span class="product-offer-tag" data-save-bhd="<?= $saveAmtVal ?>">SAVE <?= $saveAmt ?> BHD</span>
                                            <?php endif; ?>
                                    <?php else: 
                                            $discountPct = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                                            if ($discountPct > 0):
                                    ?>
                                                <span class="product-offer-tag"><?= $discountPct ?>% OFF</span>
                                            <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <a href="<?= BASE_URL ?>/product/<?= $product['slug'] ?>">
                                    <img src="<?= str_replace('/high/', '/thumb/', $product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="primary-img" loading="lazy" width="400" height="600">
                                    <img src="<?= str_replace('/high/', '/thumb/', $product['secondary_image'] ?: $product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="secondary-img" loading="lazy" width="400" height="600">
                                </a>

                                <!-- Small Corner '+' Box -->
                                <a href="<?= BASE_URL ?>/product/<?= $product['slug'] ?>" class="quick-plus-btn" title="View Product Details">+</a>
                            </div>

                            <!-- Clean Minimalist Product Info Below Image -->
                            <div class="product-info-minimal">
                                <h3 class="product-title-minimal">
                                    <a href="<?= BASE_URL ?>/product/<?= $product['slug'] ?>">
                                        <?php if(isset($currentLang) && $currentLang === 'ar' && !empty($product['name_ar'])): ?>
                                            <?= htmlspecialchars($product['name_ar']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($product['name']) ?>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($product['product_code']) ?>
                                    </a>
                                </h3>
                                <div class="product-price-minimal">
                                    <?php if (!empty($product['sale_price']) && $product['sale_price'] > 0 && $product['price'] > $product['sale_price']): ?>
                                        <span class="product-price sale" data-price-bhd="<?= $product['sale_price'] ?>"><?= number_format($product['sale_price'], 3) ?> BHD</span>
                                        <span style="text-decoration: line-through; color: #64748b; font-size: 11px; margin-left: 6px;" data-price-bhd="<?= $product['price'] ?>"><?= number_format($product['price'], 3) ?> BHD</span>
                                    <?php else: ?>
                                        <span class="product-price" data-price-bhd="<?= $product['price'] ?>"><?= number_format($product['price'], 3) ?> BHD</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Right Arrow Button -->
            <button class="slider-arrow-btn next-btn" id="featuredNextBtn" title="Slide Right" aria-label="Next Product">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"></path></svg>
            </button>
        </div>

        <!-- Solid Black VIEW ALL Button -->
        <div style="text-align: center; margin-top: 32px;">
            <a href="<?= BASE_URL ?>/collections/all-abaya" class="btn-view-all">VIEW ALL</a>
        </div>
    </div>
</section>

<!-- Category Banner Grid (Explore Collections) -->
<section style="padding: 50px 0; border-top: 1px solid var(--color-border);">
    <div class="full-width-container">
        <!-- DISCOVER BY CATEGORY on Top of EXPLORE COLLECTIONS with Identical Font Size -->
        <div class="section-title-wrap" style="margin-bottom: 36px;">
            <span style="font-family: var(--heading-font-family); font-size: 11px; letter-spacing: 0.25em; color: var(--color-accent); font-weight: 700; display: block; margin-bottom: 6px;">DISCOVER BY CATEGORY</span>
            <h2 class="section-title">EXPLORE COLLECTIONS</h2>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>/collections/<?= $cat['slug'] ?>" class="category-card">
                    <img src="<?= $cat['image'] ?>" alt="<?= htmlspecialchars($cat['name']) ?>" loading="lazy" width="600" height="800">
                    <div class="category-card-overlay">
                        <h3 class="category-card-title"><?= htmlspecialchars($cat['name']) ?></h3>
                        <span style="font-family: var(--heading-font-family); font-size: 11px; letter-spacing: 0.15em; color: var(--color-accent); text-transform: uppercase;">View Products →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
