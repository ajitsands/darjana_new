<?php
function getColorSwatchStyle($colorName) {
    $colorName = trim($colorName);
    $map = [
        'black' => '#181818',
        'red' => '#e53e3e',
        'blue' => '#3182ce',
        'green' => '#38a169',
        'beige' => '#e2d4c0',
        'navy blue' => '#1a365d',
        'navy' => '#1a365d',
        'white' => '#ffffff',
        'ivory' => '#fdfbf7',
        'gold' => '#d69e2e',
        'brown' => '#744210',
        'gray' => '#718096',
        'grey' => '#718096',
        'nude' => '#e8c4b8'
    ];

    // Check if multi-color combination (e.g. Green & Red, Blue & Gray)
    if (strpos($colorName, '&') !== false || stristr($colorName, ' and ') !== false) {
        $parts = preg_split('/(&|\band\b)/i', $colorName);
        $c1 = strtolower(trim($parts[0] ?? 'black'));
        $c2 = strtolower(trim($parts[1] ?? 'red'));

        $hex1 = $map[$c1] ?? '#38a169';
        $hex2 = $map[$c2] ?? '#e53e3e';

        return "background: linear-gradient(135deg, {$hex1} 50%, {$hex2} 50%); border: 1px solid rgba(0,0,0,0.2);";
    }

    $c = strtolower($colorName);
    $hex = $map[$c] ?? '#c5a059';
    $border = ($c === 'white' || $c === 'ivory' || $c === 'beige') ? 'border: 1px solid #ccc;' : 'border: 1px solid rgba(0,0,0,0.15);';

    return "background-color: {$hex}; {$border}";
}
?>

<div class="container" style="padding: 50px 20px 80px;">
    <!-- Breadcrumb -->
    <div style="font-size: 12px; font-family: var(--heading-font-family); color: var(--color-text-muted); margin-bottom: 30px; letter-spacing: 0.1em; text-transform: uppercase;">
        <a href="<?= BASE_URL ?>">Home</a> / 
        <a href="<?= BASE_URL ?>/collections/<?= $product['category_slug'] ?>"><?= htmlspecialchars($product['category_name']) ?></a> / 
        <span style="color: var(--color-primary);"><?= htmlspecialchars($product['name']) ?></span>
    </div>

    <!-- Product Detail Responsive Layout Grid -->
<?php
$mediaItems = [];
if (!empty($product['image'])) {
    $mediaItems[] = ['type' => 'image', 'thumb' => $product['image'], 'high' => $product['image']];
}
if (!empty($product['secondary_image']) && $product['secondary_image'] !== $product['image']) {
    $mediaItems[] = ['type' => 'image', 'thumb' => $product['secondary_image'], 'high' => $product['secondary_image']];
}
if (!empty($product['media'])) {
    $galleryMedia = json_decode($product['media'], true);
    if (is_array($galleryMedia)) {
        foreach ($galleryMedia as $item) {
            $mediaItems[] = $item;
        }
    }
}
$uniqueMedia = [];
$seenUrls = [];
foreach ($mediaItems as $item) {
    $url = $item['high'] ?? $item['url'] ?? '';
    if (!in_array($url, $seenUrls)) {
        $seenUrls[] = $url;
        $uniqueMedia[] = $item;
    }
}
$mediaItems = $uniqueMedia;
if (empty($mediaItems)) {
    $mediaItems[] = ['type' => 'image', 'thumb' => BASE_URL . '/public/assets/images/placeholder.jpg', 'high' => BASE_URL . '/public/assets/images/placeholder.jpg'];
}
?>
    <div class="product-detail-layout">
        
        <!-- Image Gallery & Zoom Column -->
        <div class="product-gallery-column">
            <div class="product-zoom-container" id="productZoomContainer" title="Hover to Zoom or Click to Enlarge">
                <?php if (!empty($product['sale_price'])): ?>
                    <?php 
                        $tagType = $product['offer_tag_type'] ?? 'percentage';
                        if ($tagType === 'amount'):
                            $saveAmt = number_format(($product['price'] - $product['sale_price']), 3);
                    ?>
                        <span class="product-offer-tag" data-save-bhd="<?= $product['price'] - $product['sale_price'] ?>">SAVE <?= $saveAmt ?> BHD</span>
                    <?php else: 
                            $discountPct = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                    ?>
                        <span class="product-offer-tag"><?= $discountPct ?>% OFF</span>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Hover Zoom Magnifier Wrapper -->
                <div class="zoom-magnifier-wrap" id="zoomMagnifierWrap">
                    <img id="mainProductImg" src="<?= $mediaItems[0]['thumb'] ?>" style="display: <?= $mediaItems[0]['type'] === 'image' ? 'block' : 'none' ?>;" alt="<?= htmlspecialchars($product['name']) ?>" class="main-zoom-img">
                    <video id="mainProductVideo" controls style="width: 100%; height: 100%; object-fit: cover; display: <?= $mediaItems[0]['type'] === 'video' ? 'block' : 'none' ?>;">
                        <source id="mainProductVideoSrc" src="<?= $mediaItems[0]['type'] === 'video' ? $mediaItems[0]['url'] : '' ?>">
                    </video>
                </div>

                <!-- Click to Enlarge Hint Badge -->
                <div class="zoom-hint-badge">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m-3-3h6"></path></svg>
                    <span>CLICK FOR FULLSCREEN ZOOM</span>
                </div>
            </div>

            <!-- Thumbnail Gallery -->
            <?php if (count($mediaItems) > 1): ?>
                <div class="thumbnail-gallery-scroll" style="display: flex; gap: 14px; margin-top: 16px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: thin;">
                    <?php foreach ($mediaItems as $index => $item): ?>
                        <?php if ($item['type'] === 'video'): ?>
                            <div class="gallery-thumb-img <?= $index === 0 ? 'active' : '' ?>" style="position: relative; display:flex; align-items:center; justify-content:center; background:#000; cursor:pointer;" data-index="<?= $index ?>" onclick="switchProductMedia(this, <?= $index ?>)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="position:absolute; z-index:2; opacity:0.8;"><path d="M8 5v14l11-7z"/></svg>
                                <video style="width:100%; height:100%; object-fit:cover; opacity:0.6;"><source src="<?= $item['url'] ?>"></video>
                            </div>
                        <?php else: ?>
                            <img src="<?= $item['tiny'] ?? $item['thumb'] ?>" class="gallery-thumb-img <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>" onclick="switchProductMedia(this, <?= $index ?>)">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <style>
                    .thumbnail-gallery-scroll::-webkit-scrollbar { height: 6px; }
                    .thumbnail-gallery-scroll::-webkit-scrollbar-thumb { background: #d0d0d0; border-radius: 4px; }
                </style>
            <?php endif; ?>
        </div>

        <!-- Product Details Info Column -->
        <div class="product-info-column">
            <div class="product-code" style="font-size: 13px; margin-bottom: 8px;">PRODUCT CODE: <?= htmlspecialchars($product['product_code']) ?></div>
            <?php
                $currentLang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';
                $displayName = ($currentLang === 'ar' && !empty($product['name_ar'])) ? $product['name_ar'] : $product['name'];
            ?>
            <h1 style="font-size: 26px; font-weight: 400; line-height: 1.3; margin-bottom: 16px; color: var(--color-primary);"><?= htmlspecialchars($displayName) ?></h1>
            
            <div class="product-price-wrap" style="margin-bottom: 20px;">
                <?php if ($product['sale_price']): ?>
                    <span class="product-price sale" style="font-size: 24px; font-weight: 700;" data-price-bhd="<?= $product['sale_price'] ?>"><?= number_format($product['sale_price'], 3) ?> BHD</span>
                    <span class="product-price-old" style="font-size: 18px; text-decoration: line-through; color: #999; margin-left: 8px;" data-price-bhd="<?= $product['price'] ?>"><?= number_format($product['price'], 3) ?> BHD</span>
                <?php else: ?>
                    <span class="product-price" style="font-size: 24px; font-weight: 600;" data-price-bhd="<?= $product['price'] ?>"><?= number_format($product['price'], 3) ?> BHD</span>
                <?php endif; ?>
            </div>

            <!-- THIN SEPARATOR LINE AFTER PRICE -->
            <div style="border-top: 1px solid var(--color-border); margin-bottom: 20px;"></div>

            <!-- PRODUCT DETAILS & DESCRIPTION -->
            <div style="margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--color-border);">
                <?php if ($currentLang === 'ar' && !empty($product['description_ar'])): ?>
                    <p dir="rtl" style="color: var(--color-primary); font-size: 15px; line-height: 1.9; margin: 0; font-family: 'Noto Naskh Arabic', 'Arial', sans-serif; text-align: right;">
                        <?= nl2br(htmlspecialchars($product['description_ar'])) ?>
                    </p>
                <?php else: ?>
                    <p style="color: var(--color-text-muted); font-size: 14.5px; line-height: 1.7; margin: 0;">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 24px;">
                <button type="button" onclick="openSizeGuide()" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: #f8f8f8; border: 1px solid #dcdcdc; border-radius: 4px; font-family: var(--heading-font-family); font-size: 13px; letter-spacing: 0.1em; font-weight: 600; cursor: pointer; color: var(--color-primary); transition: all 0.2s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    SIZE GUIDE
                </button>
            </div>

            <!-- COLOR & MULTI-COLOR COMBINATION SELECTOR WITH SWATCH DOTS -->
            <div style="margin-bottom: 24px;">
                <label style="font-family: var(--heading-font-family); font-size: 12px; letter-spacing: 0.15em; font-weight: 600; display: block; margin-bottom: 8px;">
                    COLOR / COMBINATION: <span id="activeColorLabel" style="color: var(--color-accent); font-weight: 700;"></span>
                </label>
                <div class="variant-chip-group">
                    <?php 
                    $colorsRaw = $product['colors'] ?: 'Black, Red, Green & Red, Blue & Gray, Beige';
                    $parsedColors = json_decode($colorsRaw, true);
                    $colorsList = [];
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsedColors)) {
                        foreach ($parsedColors as $pc) {
                            $colorsList[] = [
                                'name' => $pc['name'],
                                'style' => isset($pc['color3']) && !empty($pc['color3'])
                                    ? "background: linear-gradient(135deg, {$pc['color1']} 33%, {$pc['color2']} 33% 66%, {$pc['color3']} 66%); border: 1px solid rgba(0,0,0,0.2);"
                                    : (isset($pc['color2']) && !empty($pc['color2']) 
                                        ? "background: linear-gradient(135deg, {$pc['color1']} 50%, {$pc['color2']} 50%); border: 1px solid rgba(0,0,0,0.2);"
                                        : "background-color: {$pc['color1']}; border: 1px solid rgba(0,0,0,0.15);")
                            ];
                        }
                    } else {
                        // Fallback for old comma-separated strings
                        $rawList = array_map('trim', explode(',', $colorsRaw));
                        foreach ($rawList as $cName) {
                            $colorsList[] = [
                                'name' => $cName,
                                'style' => getColorSwatchStyle($cName)
                            ];
                        }
                    }
                    
                    foreach ($colorsList as $idx => $colObj): 
                    ?>
                        <div class="color-chip <?= $idx === 0 ? 'active' : '' ?>" data-color="<?= htmlspecialchars($colObj['name']) ?>">
                            <span class="color-swatch-dot" style="<?= $colObj['style'] ?>"></span>
                            <span><?= htmlspecialchars($colObj['name']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ABAYA SIZE SELECTOR -->
            <div style="margin-bottom: 24px;">
                <label style="font-family: var(--heading-font-family); font-size: 12px; letter-spacing: 0.15em; font-weight: 600; display: block; margin-bottom: 8px;">
                    SELECT SIZE: <span id="activeSizeLabel" style="color: var(--color-accent); font-weight: 700;"></span>
                </label>
                <div class="variant-chip-group">
                    <?php 
                    $sizesList = array_map('trim', explode(',', $product['sizes'] ?: 'S, M, L, XL, XXL'));
                    foreach ($sizesList as $idx => $sz): 
                    ?>
                        <div class="size-chip <?= $idx === 1 ? 'active' : '' ?>" data-size="<?= htmlspecialchars($sz) ?>">
                            <?= htmlspecialchars($sz) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- LENGTH SELECTOR (INCHES) -->
            <div style="margin-bottom: 24px;">
                <label style="font-family: var(--heading-font-family); font-size: 12px; letter-spacing: 0.15em; font-weight: 600; display: block; margin-bottom: 8px;">
                    SELECT LENGTH: <span id="activeLengthLabel" style="color: var(--color-accent); font-weight: 700;"></span>
                </label>
                <div class="variant-chip-group">
                    <?php 
                    $lengthsList = array_map('trim', explode(',', $product['lengths'] ?: '52, 54, 55, 56, 57, 58, 60'));
                    foreach ($lengthsList as $idx => $len): 
                    ?>
                        <div class="length-chip <?= $idx === 3 ? 'active' : '' ?>" data-length="<?= htmlspecialchars($len) ?>">
                            <?= htmlspecialchars($len) ?>"
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Add to Cart / Buy Now Form -->
            <form class="add-to-cart-form" style="margin-bottom: 30px;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="color" id="selectedColorInput" value="<?= htmlspecialchars($colorsList[0]['name'] ?? 'Black') ?>">
                <input type="hidden" name="size" id="selectedSizeInput" value="<?= htmlspecialchars($sizesList[1] ?? 'M') ?>">
                <input type="hidden" name="length" id="selectedLengthInput" value="<?= htmlspecialchars($lengthsList[3] ?? '56') ?>">

                <!-- QUANTITY SELECTOR IMMEDIATELY AFTER SELECT LENGTH -->
                <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 24px;">
                    <label style="font-family: var(--heading-font-family); font-size: 12px; letter-spacing: 0.15em; font-weight: 600;">QUANTITY:</label>
                    <div class="qty-btn-group" style="height: 44px;">
                        <button type="button" class="qty-btn" onclick="let input=this.nextElementSibling; if(parseInt(input.value)>1) input.value=parseInt(input.value)-1;">-</button>
                        <input type="text" name="quantity" value="1" readonly class="qty-val" style="border: none; outline: none; height: 100%;">
                        <button type="button" class="qty-btn" onclick="let input=this.previousElementSibling; input.value=parseInt(input.value)+1;">+</button>
                    </div>
                </div>

                <!-- SPECIAL INSTRUCTIONS / CUSTOMIZATION NOTES TEXTAREA -->
                <div style="margin-bottom: 24px;">
                    <label for="customNoteInput" style="font-family: var(--heading-font-family); font-size: 11.5px; letter-spacing: 0.15em; font-weight: 600; display: block; margin-bottom: 8px; color: var(--color-primary); text-transform: uppercase;">
                        SPECIAL INSTRUCTIONS / ORDER NOTES (OPTIONAL):
                    </label>
                    <textarea name="note" id="customNoteInput" placeholder="Add custom measurements, sleeve preferences, embroidery requests, or special instructions..." style="width: 100%; height: 74px; padding: 12px; border: 1.5px solid var(--color-border); border-radius: 4px; font-size: 13.5px; font-family: var(--text-font-family); outline: none; background: #ffffff; resize: vertical; box-shadow: 0 2px 6px rgba(0,0,0,0.02);"></textarea>
                </div>

                <!-- ADD TO CART & BUY NOW BUTTONS -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <button type="submit" class="btn-primary" style="padding: 16px; background-color: #1a1a1a; border-color: #1a1a1a;">Add To Cart</button>
                    <button type="submit" class="btn-primary btn-buy-now" style="padding: 16px; background-color: var(--color-accent); border-color: var(--color-accent);">Buy Now</button>
                </div>
            </form>



        </div>

    </div>

    <!-- Related Products — Horizontal Scroll Carousel -->
    <?php if (!empty($relatedProducts)): ?>
        <div style="margin-top: 20px; border-top: 1px solid var(--color-border); padding-top: 40px;">

            <!-- Section Header — fully centered title -->
            <div style="text-align: center; margin-bottom: 28px;">
                <span style="font-family: var(--heading-font-family); font-size: 11px; letter-spacing: 0.25em; color: var(--color-accent); font-weight: 700; display: block; margin-bottom: 6px;">SIMILAR DESIGNS</span>
                <h2 style="font-family: var(--heading-font-family); font-size: 22px; font-weight: 400; letter-spacing: 0.1em; margin: 0;">YOU MAY ALSO LIKE</h2>
            </div>

            <!-- Carousel Wrapper with absolute arrow buttons -->
            <div style="position: relative;">

                <!-- Left Arrow -->
                <button onclick="scrollRelated(-1)" style="position: absolute; left: -20px; top: 50%; transform: translateY(-60%); z-index: 10; width: 44px; height: 44px; border-radius: 50%; border: 1.5px solid #d0d0d0; background: #fff; font-size: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.10); transition: all 0.2s;" onmouseover="this.style.background='#1a1a1a';this.style.color='#fff';this.style.borderColor='#1a1a1a';" onmouseout="this.style.background='#fff';this.style.color='inherit';this.style.borderColor='#d0d0d0';">‹</button>

                <!-- Scrollable Carousel Track -->
                <div id="relatedCarousel" style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 8px; cursor: grab;">
                    <?php foreach ($relatedProducts as $relProduct): ?>
                        <div style="flex: 0 0 280px; min-width: 280px;">
                            <div style="position: relative; width: 100%; padding-top: 135%; overflow: hidden; background-color: #e5e5e5;">
                                <a href="<?= BASE_URL ?>/product/<?= $relProduct['slug'] ?>" style="position: absolute; inset: 0; display: block;">
                                    <img src="<?= $relProduct['image'] ?>"
                                         alt="<?= htmlspecialchars($relProduct['name']) ?>"
                                         loading="lazy"
                                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;"
                                         onmouseover="this.style.transform='scale(1.06)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                </a>
                                <a href="<?= BASE_URL ?>/product/<?= $relProduct['slug'] ?>" class="quick-plus-btn" title="View Product Details">+</a>
                            </div>
                            <div style="padding: 12px 2px 0;">
                                <h3 style="font-size: 11.5px; font-weight: 400; margin: 0 0 5px; line-height: 1.4;">
                                    <a href="<?= BASE_URL ?>/product/<?= $relProduct['slug'] ?>" style="color: var(--color-primary); text-decoration: none;">
                                        <?= htmlspecialchars($relProduct['name']) ?>
                                    </a>
                                </h3>
                                <div style="font-size: 11px; color: var(--color-accent); font-weight: 700; margin-bottom: 4px; font-family: var(--heading-font-family); letter-spacing: 0.08em;">
                                    <?= htmlspecialchars($relProduct['product_code']) ?>
                                </div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--color-primary);">
                                    <?php if ($relProduct['sale_price']): ?>
                                        <span class="product-price" data-price-bhd="<?= $relProduct['sale_price'] ?>"><?= number_format($relProduct['sale_price'], 3) ?> BHD</span>
                                        <span style="text-decoration: line-through; font-size: 12px; color: #aaa; margin-left: 6px;" data-price-bhd="<?= $relProduct['price'] ?>"><?= number_format($relProduct['price'], 3) ?> BHD</span>
                                    <?php else: ?>
                                        <span class="product-price" data-price-bhd="<?= $relProduct['price'] ?>"><?= number_format($relProduct['price'], 3) ?> BHD</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Right Arrow -->
                <button onclick="scrollRelated(1)" style="position: absolute; right: -20px; top: 50%; transform: translateY(-60%); z-index: 10; width: 44px; height: 44px; border-radius: 50%; border: 1.5px solid #d0d0d0; background: #fff; font-size: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.10); transition: all 0.2s;" onmouseover="this.style.background='#1a1a1a';this.style.color='#fff';this.style.borderColor='#1a1a1a';" onmouseout="this.style.background='#fff';this.style.color='inherit';this.style.borderColor='#d0d0d0';">›</button>

            </div><!-- end .carousel wrapper -->
        </div>

        <style>
            #relatedCarousel::-webkit-scrollbar { display: none; }
            #relatedCarousel.dragging { cursor: grabbing; scroll-behavior: auto; }
        </style>

        <script>
            // Arrow button scroll
            function scrollRelated(dir) {
                const track = document.getElementById('relatedCarousel');
                track.scrollBy({ left: dir * 500, behavior: 'smooth' });
            }

            // Mouse drag to scroll
            (function () {
                const track = document.getElementById('relatedCarousel');
                if (!track) return;
                let isDown = false, startX, scrollLeft;

                track.addEventListener('mousedown', e => {
                    isDown = true;
                    track.classList.add('dragging');
                    startX = e.pageX - track.offsetLeft;
                    scrollLeft = track.scrollLeft;
                });
                track.addEventListener('mouseleave', () => { isDown = false; track.classList.remove('dragging'); });
                track.addEventListener('mouseup',    () => { isDown = false; track.classList.remove('dragging'); });
                track.addEventListener('mousemove', e => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - track.offsetLeft;
                    track.scrollLeft = scrollLeft - (x - startX) * 1.5;
                });
            })();
        </script>
    <?php endif; ?>
</div>

<!-- Fullscreen Lightbox Zoom Modal with Image Navigation & Extra Zoom Toolbar -->
<div class="product-lightbox-modal" id="productLightboxModal">
    <!-- Lightbox Header Controls Toolbar -->
    <div class="lightbox-toolbar">
        <div class="lightbox-counter" id="lightboxCounter">1 / 1</div>
        
        <div class="lightbox-controls">
            <button class="lightbox-btn" id="zoomOutBtn" title="Zoom Out (-)">-</button>
            <span class="zoom-level-badge" id="zoomLevelBadge">100%</span>
            <button class="lightbox-btn" id="zoomInBtn" title="Zoom In (+)">+</button>
            <button class="lightbox-btn" id="zoomResetBtn" title="Reset Zoom">Reset</button>
            <button class="lightbox-close-btn" id="lightboxCloseBtn" title="Close Lightbox (Esc)">✕</button>
        </div>
    </div>

    <!-- Previous Image Arrow Button -->
    <button class="lightbox-arrow-btn prev-arrow" id="lightboxPrevBtn" title="Previous Image (←)">‹</button>

    <!-- Lightbox Main Image Viewing Container -->
    <div class="lightbox-content-wrap" id="lightboxContentWrap">
        <img id="lightboxZoomImg" src="" alt="High Resolution Zoom View">
    </div>

    <!-- Next Image Arrow Button -->
    <button class="lightbox-arrow-btn next-arrow" id="lightboxNextBtn" title="Next Image (→)">›</button>
</div>

<script>
    const mediaItems = <?= json_encode($mediaItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]' ?>;

    let currentImgIndex = 0;
    let currentZoomScale = 1.0;

    document.addEventListener('DOMContentLoaded', function () {
        // Variant Chips Selection Handlers (Color, Size, Length)
        const colorChips = document.querySelectorAll('.color-chip');
        const sizeChips = document.querySelectorAll('.size-chip');
        const lengthChips = document.querySelectorAll('.length-chip');

        const activeColorLabel = document.getElementById('activeColorLabel');
        const activeSizeLabel = document.getElementById('activeSizeLabel');
        const activeLengthLabel = document.getElementById('activeLengthLabel');

        const selectedColorInput = document.getElementById('selectedColorInput');
        const selectedSizeInput = document.getElementById('selectedSizeInput');
        const selectedLengthInput = document.getElementById('selectedLengthInput');

        // Color Chips
        colorChips.forEach(chip => {
            if (chip.classList.contains('active') && activeColorLabel) {
                activeColorLabel.textContent = chip.dataset.color;
            }
            chip.addEventListener('click', function () {
                colorChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                if (selectedColorInput) selectedColorInput.value = this.dataset.color;
                if (activeColorLabel) activeColorLabel.textContent = this.dataset.color;
            });
        });

        // Size Chips
        sizeChips.forEach(chip => {
            if (chip.classList.contains('active') && activeSizeLabel) {
                activeSizeLabel.textContent = chip.dataset.size;
            }
            chip.addEventListener('click', function () {
                sizeChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                if (selectedSizeInput) selectedSizeInput.value = this.dataset.size;
                if (activeSizeLabel) activeSizeLabel.textContent = this.dataset.size;
            });
        });

        // Length Chips
        lengthChips.forEach(chip => {
            if (chip.classList.contains('active') && activeLengthLabel) {
                activeLengthLabel.textContent = `${chip.dataset.length}"`;
            }
            chip.addEventListener('click', function () {
                lengthChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                if (selectedLengthInput) selectedLengthInput.value = this.dataset.length;
                if (activeLengthLabel) activeLengthLabel.textContent = `${this.dataset.length}"`;
            });
        });

        // Hover & Lightbox Zoom Logic
        const container = document.getElementById('productZoomContainer');
        const mainImg = document.getElementById('mainProductImg');
        const lightboxModal = document.getElementById('productLightboxModal');
        const lightboxImg = document.getElementById('lightboxZoomImg');
        const lightboxClose = document.getElementById('lightboxCloseBtn');
        const lightboxPrev = document.getElementById('lightboxPrevBtn');
        const lightboxNext = document.getElementById('lightboxNextBtn');
        const lightboxCounter = document.getElementById('lightboxCounter');
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const zoomResetBtn = document.getElementById('zoomResetBtn');
        const zoomLevelBadge = document.getElementById('zoomLevelBadge');

        if (mediaItems.length <= 1) {
            if (lightboxPrev) lightboxPrev.style.display = 'none';
            if (lightboxNext) lightboxNext.style.display = 'none';
        }

        if (container && mainImg) {
            container.addEventListener('mousemove', function (e) {
                if (mediaItems[currentImgIndex].type === 'video') return;
                const rect = container.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                mainImg.style.transformOrigin = `${x}% ${y}%`;
                mainImg.style.transform = 'scale(2.2)';
            });

            container.addEventListener('mouseleave', function () {
                if (mediaItems[currentImgIndex].type === 'video') return;
                mainImg.style.transformOrigin = 'center center';
                mainImg.style.transform = 'scale(1)';
            });

            container.addEventListener('click', function () {
                if (mediaItems[currentImgIndex].type === 'video') return;
                openLightbox(currentImgIndex);
            });
        }

        function openLightbox(index) {
            currentImgIndex = index;
            currentZoomScale = 1.0;
            updateLightboxView();
            lightboxModal.classList.add('active');
        }

        function updateLightboxView() {
            if (!mediaItems[currentImgIndex]) return;
            
                let attempts = 0;
                while (mediaItems[currentImgIndex].type !== 'image' && attempts < mediaItems.length) {
                    currentImgIndex = (currentImgIndex + 1) % mediaItems.length;
                    attempts++;
                }
                lightboxImg.src = mediaItems[currentImgIndex].high;
            if (lightboxCounter) {
                lightboxCounter.textContent = `${currentImgIndex + 1} / ${mediaItems.length}`;
            }
            applyZoomScale();
        }

        // --- PAN STATE ---
        let panX = 0, panY = 0;
        let isDragging = false;
        let dragStartX = 0, dragStartY = 0;
        let panStartX = 0, panStartY = 0;

        function applyZoomScale() {
            lightboxImg.style.transform = `scale(${currentZoomScale}) translate(${panX / currentZoomScale}px, ${panY / currentZoomScale}px)`;
            lightboxImg.style.transformOrigin = 'center center';
            lightboxImg.style.cursor = currentZoomScale > 1 ? (isDragging ? 'grabbing' : 'grab') : 'default';
            if (zoomLevelBadge) {
                zoomLevelBadge.textContent = `${Math.round(currentZoomScale * 100)}%`;
            }
        }

        function resetPan() {
            panX = 0;
            panY = 0;
        }

        // --- MOUSE DRAG TO PAN ---
        const lightboxWrap = document.getElementById('lightboxContentWrap');
        if (lightboxWrap) {
            lightboxWrap.addEventListener('mousedown', function (e) {
                if (currentZoomScale <= 1) return;
                e.preventDefault();
                isDragging = true;
                dragStartX = e.clientX;
                dragStartY = e.clientY;
                panStartX = panX;
                panStartY = panY;
                lightboxImg.style.cursor = 'grabbing';
            });

            lightboxWrap.addEventListener('mousemove', function (e) {
                if (!isDragging) return;
                e.preventDefault();
                panX = panStartX + (e.clientX - dragStartX);
                panY = panStartY + (e.clientY - dragStartY);
                applyZoomScale();
            });

            lightboxWrap.addEventListener('mouseup', function () {
                isDragging = false;
                if (currentZoomScale > 1) lightboxImg.style.cursor = 'grab';
            });

            lightboxWrap.addEventListener('mouseleave', function () {
                isDragging = false;
                if (currentZoomScale > 1) lightboxImg.style.cursor = 'grab';
            });

            // --- TOUCH DRAG TO PAN (Mobile) ---
            let touchStartX = 0, touchStartY = 0;
            let touchPanStartX = 0, touchPanStartY = 0;

            lightboxWrap.addEventListener('touchstart', function (e) {
                if (currentZoomScale <= 1 || e.touches.length !== 1) return;
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                touchPanStartX = panX;
                touchPanStartY = panY;
            }, { passive: true });

            lightboxWrap.addEventListener('touchmove', function (e) {
                if (currentZoomScale <= 1 || e.touches.length !== 1) return;
                e.preventDefault();
                panX = touchPanStartX + (e.touches[0].clientX - touchStartX);
                panY = touchPanStartY + (e.touches[0].clientY - touchStartY);
                applyZoomScale();
            }, { passive: false });

            // --- SCROLL WHEEL ZOOM ---
            lightboxWrap.addEventListener('wheel', function (e) {
                e.preventDefault();
                const delta = e.deltaY < 0 ? 0.25 : -0.25;
                const newScale = Math.min(3.5, Math.max(1.0, currentZoomScale + delta));
                if (newScale === 1.0) resetPan();
                currentZoomScale = newScale;
                applyZoomScale();
                if (zoomLevelBadge) zoomLevelBadge.textContent = `${Math.round(currentZoomScale * 100)}%`;
            }, { passive: false });
        }

        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (currentZoomScale < 3.5) {
                    currentZoomScale += 0.5;
                    applyZoomScale();
                }
            });
        }

        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (currentZoomScale > 1.0) {
                    currentZoomScale -= 0.5;
                    applyZoomScale();
                }
            });
        }

        if (zoomResetBtn) {
            zoomResetBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                currentZoomScale = 1.0;
                resetPan();
                applyZoomScale();
            });
        }

        if (lightboxImg) {
            lightboxImg.addEventListener('dblclick', function (e) {
                e.stopPropagation();
                if (currentZoomScale > 1.0) {
                    currentZoomScale = 1.0;
                } else {
                    currentZoomScale = 2.5;
                }
                applyZoomScale();
            });
        }

        function nextImage() {
            currentImgIndex = (currentImgIndex + 1) % mediaItems.length;
            currentZoomScale = 1.0;
            resetPan();
            updateLightboxView();
            syncGalleryThumbnails();
        }

        function prevImage() {
            currentImgIndex = (currentImgIndex - 1 + mediaItems.length) % mediaItems.length;
            currentZoomScale = 1.0;
            resetPan();
            updateLightboxView();
            syncGalleryThumbnails();
        }

        if (lightboxNext) lightboxNext.addEventListener('click', function (e) { e.stopPropagation(); nextImage(); });
        if (lightboxPrev) lightboxPrev.addEventListener('click', function (e) { e.stopPropagation(); prevImage(); });

        if (lightboxClose) {
            lightboxClose.addEventListener('click', function () {
                lightboxModal.classList.remove('active');
            });
        }

        if (lightboxModal) {
            let didDragPan = false;
            lightboxModal.addEventListener('mousedown', () => { didDragPan = false; });
            lightboxModal.addEventListener('mousemove', (e) => { if (e.buttons === 1) didDragPan = true; });
            lightboxModal.addEventListener('click', function (e) {
                if (didDragPan) { didDragPan = false; return; }
                if (e.target === lightboxModal) {
                    lightboxModal.classList.remove('active');
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (!lightboxModal.classList.contains('active')) return;
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
            if (e.key === 'Escape') lightboxModal.classList.remove('active');
        });
    });

    function switchProductMedia(thumbEl, index) {
        currentImgIndex = index;
        syncGalleryThumbnails();
    }

    function syncGalleryThumbnails() {
        document.querySelectorAll('.gallery-thumb-img').forEach((el, idx) => {
            if (idx === currentImgIndex) el.classList.add('active');
            else el.classList.remove('active');
        });
        
        const mainImg = document.getElementById('mainProductImg');
        const mainVideo = document.getElementById('mainProductVideo');
        const mainVideoSrc = document.getElementById('mainProductVideoSrc');
        const media = mediaItems[currentImgIndex];
        
        if (!media) return;
        
        if (media.type === 'video') {
            if (mainImg) mainImg.style.display = 'none';
            if (mainVideo) {
                mainVideo.style.display = 'block';
                if (mainVideoSrc && mainVideoSrc.src !== media.url) {
                    mainVideoSrc.src = media.url;
                    mainVideo.load();
                }
            }
        } else {
            if (mainVideo) {
                mainVideo.style.display = 'none';
                mainVideo.pause();
            }
            if (mainImg) {
                mainImg.style.display = 'block';
                if (media.thumb) {
                    mainImg.src = media.thumb;
                }
            }
        }
    }
</script>

<!-- SIZE GUIDE OFFCANVAS -->
<?php
$chestData = isset($settings['size_guide_chest']) ? json_decode($settings['size_guide_chest'], true) : [
    ['size' => 'S', 'chest' => '20.00', 'shoulder' => '27.00'],
    ['size' => 'M', 'chest' => '23.00', 'shoulder' => '28.00'],
    ['size' => 'L', 'chest' => '24.00', 'shoulder' => '29.00'],
    ['size' => 'XL', 'chest' => '25.00', 'shoulder' => '29.50'],
    ['size' => 'XXL', 'chest' => '26.00', 'shoulder' => '30.50']
];
$lengthData = isset($settings['size_guide_length']) ? json_decode($settings['size_guide_length'], true) : [
    ['length' => '49.00', 'height' => '150'],
    ['length' => '50.00', 'height' => '151'],
    ['length' => '50.00', 'height' => '152'],
    ['length' => '51.00', 'height' => '153'],
    ['length' => '51.00', 'height' => '154'],
    ['length' => '52.00', 'height' => '155'],
    ['length' => '52.00', 'height' => '156']
];
?>
<div id="sizeGuideOverlay" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 9998; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;" onclick="closeSizeGuide()"></div>
<div id="sizeGuideCanvas" style="position: fixed; top: 0; right: -500px; width: 100%; max-width: 500px; height: 100vh; background: #fff; z-index: 9999; overflow-y: auto; transition: right 0.3s ease; box-shadow: -5px 0 15px rgba(0,0,0,0.1);">
    <div style="padding: 24px; position: relative;">
        <button onclick="closeSizeGuide()" style="position: absolute; top: 20px; right: 24px; background: none; border: none; font-size: 24px; cursor: pointer; color: #1a1a1a;">&times;</button>
        
        <!-- SECTION 1 -->
        <h2 style="font-family: var(--heading-font-family); font-size: 16px; letter-spacing: 0.1em; color: #1a1a1a; margin-top: 20px; margin-bottom: 20px; text-transform: uppercase;">Size Guide - Chest & Shoulder<br><span style="font-size: 13px; color: #718096; font-weight: 400; text-transform: none;">دليل المقاسات - الصدر والكتف</span></h2>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px; font-size: 14px;">
            <thead>
                <tr style="background: #2d2d2d; color: #fff;">
                    <th style="padding: 12px; text-align: left; border: 1px solid #444;">SIZE /<br>المقاسات</th>
                    <th style="padding: 12px; text-align: left; border: 1px solid #444;">CHEST (INCH) /<br>الصدر</th>
                    <th style="padding: 12px; text-align: left; border: 1px solid #444;">SHOULDER (INCH) /<br>الكتف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($chestData as $row): ?>
                <tr>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; color: #4a5568;"><?= htmlspecialchars($row['size']) ?></td>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; color: #4a5568;"><?= htmlspecialchars($row['chest']) ?></td>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; color: #4a5568;"><?= htmlspecialchars($row['shoulder']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- SECTION 2 -->
        <h2 style="font-family: var(--heading-font-family); font-size: 16px; letter-spacing: 0.1em; color: #1a1a1a; margin-bottom: 10px; text-transform: uppercase;">Find Your Abaya Length by Height<br><span style="font-size: 13px; color: #718096; font-weight: 400; text-transform: none;">جدول طول العباءة حسب الطول</span></h2>
        <p style="font-size: 13px; color: #718096; line-height: 1.6; margin-bottom: 20px;">
            <?= nl2br(htmlspecialchars($settings['size_guide_desc_en'] ?? 'This chart shows the recommended length based on height. Please double-check with your own measurement to be sure of your perfect fit.')) ?><br><br>
            <?= nl2br(htmlspecialchars($settings['size_guide_desc_ar'] ?? 'هذا الجدول يوضح الطول الموصى به حسب طولك. يُرجى التأكد بالمتر لقياسك الشخصي للحصول على المقاس الأنسب لكِ.')) ?>
        </p>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px; font-size: 14px;">
            <thead>
                <tr style="background: #2d2d2d; color: #fff;">
                    <th style="padding: 12px; text-align: left; border: 1px solid #444;">ABAYA LENGTH (INCH)</th>
                    <th style="padding: 12px; text-align: left; border: 1px solid #444;">YOUR HEIGHT (CM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($lengthData as $row): ?>
                <tr>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; color: #4a5568;"><?= htmlspecialchars($row['length']) ?></td>
                    <td style="padding: 12px; border: 1px solid #e2e8f0; color: #4a5568;"><?= htmlspecialchars($row['height']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- MEASUREMENT TIPS -->
        <div style="background: #f8f8f8; padding: 20px; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">💡</span>
            <span style="font-family: var(--heading-font-family); font-weight: 600; letter-spacing: 0.1em; font-size: 14px;">MEASUREMENT TIPS</span>
        </div>
    </div>
</div>

<script>
    function openSizeGuide() {
        document.getElementById('sizeGuideOverlay').style.opacity = '1';
        document.getElementById('sizeGuideOverlay').style.pointerEvents = 'auto';
        document.getElementById('sizeGuideCanvas').style.right = '0';
        document.body.style.overflow = 'hidden';
    }
    function closeSizeGuide() {
        document.getElementById('sizeGuideOverlay').style.opacity = '0';
        document.getElementById('sizeGuideOverlay').style.pointerEvents = 'none';
        document.getElementById('sizeGuideCanvas').style.right = '-500px';
        document.body.style.overflow = 'auto';
    }
</script>
