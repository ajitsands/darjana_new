<div class="container" style="padding: 60px 20px 80px;">
    <div class="section-title-wrap">
        <span style="font-family: var(--heading-font-family); font-size: 12px; letter-spacing: 0.2em; color: var(--color-accent);">SEARCH RESULTS</span>
        <h1 class="section-title">Results for "<?= htmlspecialchars($query) ?>"</h1>
        <p class="section-subtitle">Found <?= count($products) ?> items matching your search</p>
    </div>

    <?php if (empty($products)): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <p style="color: #666; font-size: 16px; margin-bottom: 20px;">No products match your search keyword.</p>
            <a href="<?= BASE_URL ?>/collections/all-abaya" class="btn-primary">Browse All Abayas & Dresses</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image-wrap">
                        <a href="<?= BASE_URL ?>/product/<?= $product['slug'] ?>">
                            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="primary-img">
                        </a>
                    </div>
                    <div class="product-info">
                        <span class="product-code"><?= htmlspecialchars($product['product_code']) ?></span>
                        <h3 class="product-title">
                            <a href="<?= BASE_URL ?>/product/<?= $product['slug'] ?>"><?= htmlspecialchars($product['name']) ?></a>
                        </h3>
                        <div class="product-price-wrap">
                            <span class="product-price"><?= number_format($product['price'], 2) ?> BHD</span>
                        </div>
                        <form class="add-to-cart-form" style="margin-top: auto;">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="size" value="54">
                            <div class="card-btn-group">
                                <button type="submit" class="btn-secondary btn-card-add">Add To Cart</button>
                                <button type="submit" class="btn-primary btn-buy-now btn-card-buy">Buy Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
