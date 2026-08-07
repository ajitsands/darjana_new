<?php
// Map collection slugs to exact menu header titles
$titleMap = [
    'all-abaya' => 'ALL ABAYA',
    'black-abaya' => 'BLACK ABAYA',
    'colourful-abaya' => 'COLOURFUL ABAYA',
    'colorful-abayas' => 'COLOURFUL ABAYA',
    'sets' => 'SETS',
    'set' => 'SETS',
    'blazer' => 'BLAZER',
    'inner' => 'INNER',
    'offers' => 'OFFERS',
    'ramadan-collection' => 'RAMADAN COLLECTION'
];

$slugKey = $currentSlug ?? ($category['slug'] ?? '');
$displayTitle = $titleMap[$slugKey] ?? strtoupper($category['name'] ?? 'ALL ABAYA');
$totalCount = $productCount ?? count($products);
$loaded = $loadedCount ?? count($products);
$sort = $currentSort ?? 'featured';

$isFilterActive = ($minPrice !== null && $minPrice !== '') || ($maxPrice !== null && $maxPrice !== '');
?>

<div style="padding: 50px 0 80px; background-color: #f3f3f3;">
    <!-- Full Screen Width Container (Matching EXPLORE COLLECTIONS in Home Page) -->
    <div class="full-width-container">
        
        <!-- Clean Category Header & Product Count -->
        <div class="section-title-wrap" style="margin-bottom: 24px;">
            <h1 class="section-title" style="font-size: 22px; font-weight: 500; letter-spacing: 0.25em; text-transform: uppercase;">
                <?= htmlspecialchars($displayTitle) ?>
            </h1>
            <div style="font-family: var(--heading-font-family); font-size: 11px; letter-spacing: 0.2em; color: var(--color-text-muted); margin-top: 6px; font-weight: 600;">
                (<span id="totalProductCountLabel"><?= $totalCount ?></span> <?= $totalCount === 1 ? 'PRODUCT' : 'PRODUCTS' ?>)
            </div>
        </div>

        <!-- Filter & Sort Control Toolbar -->
        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); padding: 14px 0; margin-bottom: 44px; flex-wrap: wrap; gap: 16px;">
            
            <!-- Left Side: Filter Button -->
            <div>
                <button type="button" id="filterToggleBtn" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; font-family: var(--heading-font-family); font-size: 12px; font-weight: 600; letter-spacing: 0.18em; color: var(--color-primary); text-transform: uppercase;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M9 18h6"></path></svg>
                    <span>FILTER <?= $isFilterActive ? '(ACTIVE)' : '' ?></span>
                </button>
            </div>

            <!-- Right Side: Sort By Selector Dropdown -->
            <div style="display: flex; align-items: center; gap: 10px;">
                <label for="sortBySelect" style="font-family: var(--heading-font-family); font-size: 11.5px; font-weight: 600; letter-spacing: 0.18em; color: var(--color-text-muted); text-transform: uppercase;">SORT BY:</label>
                <form id="sortForm" method="GET" action="" style="margin: 0;">
                    <?php if ($minPrice !== null && $minPrice !== ''): ?><input type="hidden" name="min_price" value="<?= htmlspecialchars($minPrice) ?>"><?php endif; ?>
                    <?php if ($maxPrice !== null && $maxPrice !== ''): ?><input type="hidden" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>"><?php endif; ?>
                    
                    <select id="sortBySelect" name="sort" onchange="this.form.submit()" style="background: #ffffff; border: 1px solid var(--color-border); border-radius: 4px; padding: 8px 14px; font-family: var(--heading-font-family); font-size: 12px; font-weight: 500; letter-spacing: 0.08em; color: var(--color-primary); cursor: pointer; outline: none;">
                        <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Featured</option>
                        <option value="relevant" <?= $sort === 'relevant' ? 'selected' : '' ?>>Most Relevant</option>
                        <option value="best_selling" <?= $sort === 'best_selling' ? 'selected' : '' ?>>Best Selling</option>
                        <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Alphabetically, A-Z</option>
                        <option value="title_desc" <?= $sort === 'title_desc' ? 'selected' : '' ?>>Alphabetically, Z-A</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price, Low to High</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price, High to Low</option>
                        <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Date, Old to New</option>
                        <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Date, New to Old</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Filter Drawer Panel (Expandable) -->
        <div id="filterDrawerPanel" style="display: <?= $isFilterActive ? 'block' : 'none' ?>; background: #ffffff; border: 1px solid var(--color-border); padding: 24px; border-radius: 6px; margin-top: -30px; margin-bottom: 40px; box-shadow: var(--shadow-card);">
            <form method="GET" action="" style="display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap;">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                
                <div>
                    <label style="display: block; font-family: var(--heading-font-family); font-size: 11px; font-weight: 600; letter-spacing: 0.15em; color: var(--color-primary); margin-bottom: 6px; text-transform: uppercase;">Min Price (BHD)</label>
                    <input type="number" step="0.001" name="min_price" value="<?= htmlspecialchars($minPrice ?? '') ?>" placeholder="e.g. 35.000" style="padding: 8px 12px; border: 1px solid var(--color-border); border-radius: 4px; font-size: 13px; outline: none; width: 130px;">
                </div>

                <div>
                    <label style="display: block; font-family: var(--heading-font-family); font-size: 11px; font-weight: 600; letter-spacing: 0.15em; color: var(--color-primary); margin-bottom: 6px; text-transform: uppercase;">Max Price (BHD)</label>
                    <input type="number" step="0.001" name="max_price" value="<?= htmlspecialchars($maxPrice ?? '') ?>" placeholder="e.g. 50.000" style="padding: 8px 12px; border: 1px solid var(--color-border); border-radius: 4px; font-size: 13px; outline: none; width: 130px;">
                </div>

                <button type="submit" class="btn-primary" style="padding: 10px 24px; font-size: 11px;">APPLY FILTER</button>
                <a href="<?= BASE_URL ?>/collections/<?= $currentSlug ?>" style="font-size: 12px; color: var(--color-text-muted); text-decoration: underline; margin-bottom: 10px;">Clear Filter</a>
            </form>
        </div>

        <?php if (empty($products)): ?>
            <div style="text-align: center; padding: 60px 0; color: var(--color-text-muted);">
                <p>No products currently match your filter criteria.</p>
                <a href="<?= BASE_URL ?>/collections/<?= $currentSlug ?>" class="btn-primary" style="margin-top: 20px;">Clear Filter</a>
            </div>
        <?php else: ?>
            <div class="products-grid" id="mainProductsGrid">
                <?php foreach ($products as $product): ?>
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
                                <img src="<?= str_replace('/high/', '/thumb/', $product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="primary-img">
                                <img src="<?= str_replace('/high/', '/thumb/', $product['secondary_image'] ?: $product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="secondary-img">
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
                                    <span style="text-decoration: line-through; color: #999; font-size: 11px; margin-left: 6px;" data-price-bhd="<?= $product['price'] ?>"><?= number_format($product['price'], 3) ?> BHD</span>
                                <?php else: ?>
                                    <span class="product-price" data-price-bhd="<?= $product['price'] ?>"><?= number_format($product['price'], 3) ?> BHD</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Sleek LOAD MORE Mechanism & Item Progress Counter -->
            <div id="loadMoreContainer" style="text-align: center; margin-top: 50px;">
                <div style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 14px; font-weight: 500;">
                    Showing <span id="loadedCountNum"><?= $loaded ?></span> of <?= $totalCount ?> items
                </div>

                <?php if ($hasMore): ?>
                    <button type="button" id="loadMoreBtn" data-next-page="2" data-slug="<?= htmlspecialchars($currentSlug) ?>" data-sort="<?= htmlspecialchars($sort) ?>" data-min="<?= htmlspecialchars($minPrice ?? '') ?>" data-max="<?= htmlspecialchars($maxPrice ?? '') ?>" class="btn-view-all" style="min-width: 220px;">
                        LOAD MORE
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtn = document.getElementById('filterToggleBtn');
        const filterPanel = document.getElementById('filterDrawerPanel');
        if (filterBtn && filterPanel) {
            filterBtn.addEventListener('click', function () {
                if (filterPanel.style.display === 'none' || filterPanel.style.display === '') {
                    filterPanel.style.display = 'block';
                } else {
                    filterPanel.style.display = 'none';
                }
            });
        }

        // AJAX Load More Button Handler
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const mainGrid = document.getElementById('mainProductsGrid');
        const loadedCountNum = document.getElementById('loadedCountNum');
        const baseUrl = window.BASE_URL || '';

        if (loadMoreBtn && mainGrid) {
            loadMoreBtn.addEventListener('click', function () {
                const nextPage = this.dataset.nextPage;
                const slug = this.dataset.slug;
                const sort = this.dataset.sort;
                const minPrice = this.dataset.min;
                const maxPrice = this.dataset.max;

                this.textContent = 'LOADING...';
                this.disabled = true;

                let url = `${baseUrl}/collections/${slug}?ajax=1&page=${nextPage}&sort=${encodeURIComponent(sort)}`;
                if (minPrice) url += `&min_price=${encodeURIComponent(minPrice)}`;
                if (maxPrice) url += `&max_price=${encodeURIComponent(maxPrice)}`;

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.products.length > 0) {
                            data.products.forEach(p => {
                                let offerBadgeHtml = '';
                                let priceHtml = '';

                                const priceNum = parseFloat(p.price) || 0;
                                const salePriceNum = (p.sale_price && parseFloat(p.sale_price) > 0) ? parseFloat(p.sale_price) : null;
                                const hasDiscount = (salePriceNum !== null && priceNum > salePriceNum);

                                if (hasDiscount) {
                                    const saveAmtVal = priceNum - salePriceNum;
                                    if (p.offer_tag_type === 'amount' && saveAmtVal > 0) {
                                        const saveAmt = saveAmtVal.toFixed(3);
                                        offerBadgeHtml = `<span class="product-offer-tag" data-save-bhd="${saveAmtVal}">SAVE ${saveAmt} BHD</span>`;
                                    } else {
                                        const discountPct = Math.round((saveAmtVal / priceNum) * 100);
                                        if (discountPct > 0) {
                                            offerBadgeHtml = `<span class="product-offer-tag">${discountPct}% OFF</span>`;
                                        }
                                    }

                                    priceHtml = `
                                        <span class="product-price sale" data-price-bhd="${salePriceNum}">${salePriceNum.toFixed(3)} BHD</span>
                                        <span style="text-decoration: line-through; color: #999; font-size: 11px; margin-left: 6px;" data-price-bhd="${priceNum}">${priceNum.toFixed(3)} BHD</span>`;
                                } else {
                                    priceHtml = `<span class="product-price" data-price-bhd="${priceNum}">${priceNum.toFixed(3)} BHD</span>`;
                                }

                                const cardHtml = `
                                    <div class="product-card">
                                        <div class="product-image-wrap">
                                            ${offerBadgeHtml}
                                            <a href="${baseUrl}/product/${p.slug}">
                                                <img src="${p.image.replace('/high/', '/thumb/')}" alt="${p.name}" class="primary-img">
                                                <img src="${(p.secondary_image || p.image).replace('/high/', '/thumb/')}" alt="${p.name}" class="secondary-img">
                                            </a>
                                            <a href="${baseUrl}/product/${p.slug}" class="quick-plus-btn" title="View Product Details">+</a>
                                        </div>
                                        <div class="product-info-minimal">
                                            <h3 class="product-title-minimal">
                                                <a href="${baseUrl}/product/${p.slug}">${p.name} ${p.product_code}</a>
                                            </h3>
                                            <div class="product-price-minimal">
                                                ${priceHtml}
                                            </div>
                                        </div>
                                    </div>`;
                                mainGrid.insertAdjacentHTML('beforeend', cardHtml);
                            });

                            if (loadedCountNum) {
                                loadedCountNum.textContent = data.loadedCount;
                            }

                            if (data.hasMore) {
                                loadMoreBtn.dataset.nextPage = parseInt(nextPage) + 1;
                                loadMoreBtn.textContent = 'LOAD MORE';
                                loadMoreBtn.disabled = false;
                            } else {
                                loadMoreBtn.remove();
                            }

                            if (typeof window.updateCurrencyUI === 'function') {
                                window.updateCurrencyUI();
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Load More Error:', err);
                        loadMoreBtn.textContent = 'LOAD MORE';
                        loadMoreBtn.disabled = false;
                    });
            });
        }
    });
</script>
