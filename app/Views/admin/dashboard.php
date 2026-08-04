<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Store Performance Dashboard</h1>
                    <p style="color: #718096; font-size: 14px;">Manage orders, product catalog, and sales revenue for Dar Jana Fashion</p>
                </div>
            </div>

            <!-- Stats Metric Cards -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 40px;">
                <div class="stat-card">
                    <div style="font-size: 12px; font-family: var(--heading-font-family); color: #718096; letter-spacing: 0.1em;">TOTAL SALES REVENUE</div>
                    <div style="font-size: 28px; font-weight: 700; color: var(--color-accent); margin-top: 6px;"><?= $totalRevenue ?> BHD</div>
                </div>
                <div class="stat-card">
                    <div style="font-size: 12px; font-family: var(--heading-font-family); color: #718096; letter-spacing: 0.1em;">TOTAL ORDERS</div>
                    <div style="font-size: 28px; font-weight: 700; color: #181818; margin-top: 6px;"><?= $totalOrdersCount ?></div>
                </div>
                <div class="stat-card">
                    <div style="font-size: 12px; font-family: var(--heading-font-family); color: #718096; letter-spacing: 0.1em;">PRODUCTS IN CATALOG</div>
                    <div style="font-size: 28px; font-weight: 700; color: #181818; margin-top: 6px;"><?= $totalProductsCount ?></div>
                </div>
            </div>


            <!-- Add Product Form & Catalog List -->
            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px;">
                <!-- Product List -->
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 16px;">Product Catalog</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>IMAGE</th>
                                    <th>CODE & NAME</th>
                                    <th>CATEGORY</th>
                                    <th>TAG FORMAT</th>
                                    <th>PRICE</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= $p['image'] ?>" style="width: 40px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        </td>
                                        <td>
                                            <div style="font-size: 11px; color: var(--color-accent); font-weight: 700;"><?= htmlspecialchars($p['product_code']) ?></div>
                                            <div style="font-weight: 600; font-size: 13px;"><?= htmlspecialchars($p['name']) ?></div>
                                        </td>
                                        <td style="font-size: 12px;"><?= htmlspecialchars($p['category_name']) ?></td>
                                        <td>
                                            <span style="font-size: 11px; font-weight: 700; background: #e2e8f0; padding: 2px 6px; border-radius: 3px; text-transform: uppercase;">
                                                <?= htmlspecialchars($p['offer_tag_type']) ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 700; font-size: 13px;"><?= number_format($p['price'], 2) ?> BHD</td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/admin/product/edit/<?= $p['id'] ?>" style="color: var(--color-primary); font-size: 12px; font-weight: 600; margin-right: 8px;">Edit</a>
                                            <a href="<?= BASE_URL ?>/admin/product/delete/<?= $p['id'] ?>" onclick="return confirm('Delete this product?')" style="color: #e53e3e; font-size: 12px; font-weight: 600;">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Product Form -->
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 16px;">Add New Dress / Abaya</h3>
                    <div style="background: #fff; padding: 24px; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <form action="<?= BASE_URL ?>/admin/product/add" method="POST" enctype="multipart/form-data">
                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">PRODUCT NAME</label>
                                <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="e.g. Royal Black Velvet Blazer Dress">
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">PRODUCT CODE</label>
                                    <input type="text" name="product_code" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="C:6900">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">CATEGORY</label>
                                    <select name="category_id" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; background: #fff;">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">REGULAR PRICE (BHD)</label>
                                    <input type="number" step="0.01" id="regular_price" name="price" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="45.00">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">SALE PRICE (BHD)</label>
                                    <input type="number" step="0.01" id="sale_price" name="sale_price" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="Optional">
                                </div>
                            </div>

                            <!-- OFFER TAG FORMAT SELECTION (% OFF vs SAVE AMOUNT BHD) -->
                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">OFFER TAG DISPLAY FORMAT <span id="offer_tag_preview" style="margin-left: 10px; background: #e53e3e; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; display: none;"></span></label>
                                <select id="offer_tag_type" name="offer_tag_type" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; background: #fff;">
                                    <option value="percentage">Percentage Discount (% OFF e.g. 16% OFF)</option>
                                    <option value="amount">Amount Saved (SAVE BHD e.g. SAVE 10 BHD)</option>
                                </select>
                            </div>

                            <!-- VARIANT OPTIONS: COLORS & COMBINATIONS -->
                                                        <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">AVAILABLE COLORS / COMBINATIONS</label>
                                <div id="colorBuilderContainer" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 4px;">
                                    <div id="colorRows"></div>
                                    <button type="button" onclick="addColorRow()" style="margin-top: 10px; background: #fff; border: 1px dashed #cbd5e0; padding: 6px 12px; font-size: 11px; cursor: pointer; border-radius: 4px; font-weight: 600; color: #4a5568;">+ Add Color Option</button>
                                </div>
                                <input type="hidden" name="colors" id="colorsJsonOutput" value="Black, Red, Green & Red, Blue & Gray, Beige">
                            </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorsJsonOutput = document.getElementById('colorsJsonOutput');
    const colorRowsContainer = document.getElementById('colorRows');
    if(!colorsJsonOutput || !colorRowsContainer) return;
    
    let initialData = [];
    try {
        let raw = colorsJsonOutput.value;
        if (raw.startsWith('[') || raw.startsWith('{')) {
            initialData = JSON.parse(raw);
        } else {
            // Fallback for comma separated
            let parts = raw.split(',').map(s => s.trim()).filter(s => s);
            parts.forEach(p => {
                initialData.push({ name: p, color1: '#181818' });
            });
        }
    } catch(e) {}
    
    if (initialData.length === 0) {
        initialData.push({ name: 'Black', color1: '#181818' });
    }
    
    initialData.forEach(item => addColorRow(item.name, item.color1, item.color2, item.color3));
    
    // Bind form submit to update JSON
    const form = colorsJsonOutput.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            const rows = colorRowsContainer.querySelectorAll('.color-row');
            let result = [];
            rows.forEach(row => {
                let name = row.querySelector('.c-name').value.trim();
                let count = parseInt(row.querySelector('.c-count').value);
                let c1 = row.querySelector('.c-1').value;
                let c2 = row.querySelector('.c-2').value;
                let c3 = row.querySelector('.c-3').value;
                if (name) {
                    let obj = { name: name, color1: c1 };
                    if (count >= 2) obj.color2 = c2;
                    if (count === 3) obj.color3 = c3;
                    result.push(obj);
                }
            });
            colorsJsonOutput.value = JSON.stringify(result);
        });
    }
});

function addColorRow(name = '', c1 = '#181818', c2 = '', c3 = '') {
    const container = document.getElementById('colorRows');
    const numColors = (c3) ? 3 : (c2 ? 2 : 1);
    
    const div = document.createElement('div');
    div.className = 'color-row';
    div.style.display = 'flex';
    div.style.gap = '8px';
    div.style.alignItems = 'center';
    div.style.marginBottom = '8px';
    
    div.innerHTML = `
        <input type="text" class="c-name" placeholder="Color Name (e.g. Green & Red)" value="${name}" style="flex: 1; padding: 6px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 12px;">
        <select class="c-count" onchange="const v=parseInt(this.value); this.parentElement.querySelector('.c-2').style.display=(v>=2)?'inline-block':'none'; this.parentElement.querySelector('.c-3').style.display=(v===3)?'inline-block':'none';" style="padding: 6px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 12px;">
            <option value="1" ${numColors===1?'selected':''}>1 Color</option>
            <option value="2" ${numColors===2?'selected':''}>2 Colors</option>
            <option value="3" ${numColors===3?'selected':''}>3 Colors</option>
        </select>
        <input type="color" class="c-1" value="${c1}" title="Color 1" style="width: 32px; height: 32px; padding: 0; border: none; cursor: pointer;">
        <input type="color" class="c-2" value="${c2 || '#ffffff'}" title="Color 2" style="width: 32px; height: 32px; padding: 0; border: none; cursor: pointer; display: ${numColors >= 2 ? 'inline-block' : 'none'};">
        <input type="color" class="c-3" value="${c3 || '#ffffff'}" title="Color 3" style="width: 32px; height: 32px; padding: 0; border: none; cursor: pointer; display: ${numColors === 3 ? 'inline-block' : 'none'};">
        <button type="button" onclick="this.parentElement.remove()" style="background: #fed7d7; border: none; color: #c53030; cursor: pointer; padding: 6px 10px; border-radius: 4px; font-size: 11px; font-weight: bold;">X</button>
    `;
    
    container.appendChild(div);
}
</script>

                            <!-- VARIANT OPTIONS: SIZES -->
                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">AVAILABLE SIZES (Comma Separated)</label>
                                <input type="text" name="sizes" value="S, M, L, XL, XXL" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="e.g. S, M, L, XL, XXL">
                            </div>

                            <!-- VARIANT OPTIONS: LENGTHS -->
                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">AVAILABLE LENGTHS IN INCHES (Comma Separated)</label>
                                <input type="text" name="lengths" value="52, 54, 55, 56, 57, 58, 60" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="e.g. 52, 54, 55, 56, 57, 58, 60">
                            </div>

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">PRIMARY IMAGE</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; margin-bottom: 4px; color: #718096;">Upload Image File (Overrides URL)</label>
                                        <input type="file" name="primary_image_file" accept="image/*" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; margin-bottom: 4px; color: #718096;">Or Image URL</label>
                                        <input type="url" name="image" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="https://images.unsplash.com/...">
                                    </div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; background: #f8fafc; padding: 16px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--color-primary);">UPLOAD GALLERY IMAGES</label>
                                    <input type="file" name="gallery_images[]" multiple accept="image/*" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                    <div style="font-size: 10.5px; color: #64748b; margin-top: 4px;">You can select multiple high-res images. Will be automatically compressed for display.</div>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--color-primary);">UPLOAD PRODUCT VIDEO</label>
                                    <input type="file" name="product_video" accept="video/mp4,video/webm" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                    <div style="font-size: 10.5px; color: #e53e3e; margin-top: 4px; font-weight: 600;">Max Size: 5.5 MB (MP4/WebM)</div>
                                </div>
                            </div>

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">DESCRIPTION (ENGLISH)</label>
                                <textarea name="description" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; height: 70px;" placeholder="Describe fabric, cut, and embellishments..."></textarea>
                            </div>

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">الوصف بالعربي (ARABIC DESCRIPTION - OPTIONAL)</label>
                                <textarea name="description_ar" dir="rtl" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; height: 70px; font-family: 'Noto Naskh Arabic', 'Arial', sans-serif; font-size: 14px;" placeholder="أدخل وصف المنتج باللغة العربية (اختياري)..."></textarea>
                                <span style="font-size: 11px; color: #718096;">If entered, Arabic description will appear above the English description on the product page.</span>
                            </div>

                            <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" name="is_featured" value="1" id="is_featured" checked>
                                <label for="is_featured" style="font-size: 13px;">Show in Home Page Featured Collection</label>
                            </div>

                            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; border-radius: 4px;">Publish Product</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const regPriceInput = document.getElementById('regular_price');
    const salePriceInput = document.getElementById('sale_price');
    const tagTypeSelect = document.getElementById('offer_tag_type');
    const previewSpan = document.getElementById('offer_tag_preview');

    function updatePreview() {
        if (!regPriceInput || !salePriceInput || !tagTypeSelect || !previewSpan) return;
        
        const regPrice = parseFloat(regPriceInput.value);
        const salePrice = parseFloat(salePriceInput.value);
        const tagType = tagTypeSelect.value;

        if (isNaN(regPrice) || isNaN(salePrice) || salePrice >= regPrice || salePrice <= 0) {
            previewSpan.style.display = 'none';
            return;
        }

        previewSpan.style.display = 'inline-block';
        if (tagType === 'percentage') {
            const percent = Math.round(((regPrice - salePrice) / regPrice) * 100);
            previewSpan.textContent = percent + '% OFF';
        } else {
            const saved = (regPrice - salePrice).toFixed(2);
            previewSpan.textContent = 'SAVE ' + saved.replace(/\.00$/, '') + ' BHD';
        }
    }

    if (regPriceInput) regPriceInput.addEventListener('input', updatePreview);
    if (salePriceInput) salePriceInput.addEventListener('input', updatePreview);
    if (tagTypeSelect) tagTypeSelect.addEventListener('change', updatePreview);
    
    updatePreview();
});
</script>
</body>

</html>
