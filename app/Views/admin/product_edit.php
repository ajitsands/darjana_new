<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Edit Product</h1>
                    <p style="color: #718096; font-size: 14px;">Update product details, pricing, variants, and media.</p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>/admin" class="btn-primary" style="background: #e2e8f0; color: #1a202c;">Back to Dashboard</a>
                </div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div style="background: #fed7d7; color: #c53030; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                    Please fill out all required fields correctly.
                </div>
            <?php endif; ?>

            <div style="background: #fff; padding: 30px; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px;">
                <form id="editProductForm" action="<?= BASE_URL ?>/admin/product/edit/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
                    
                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">PRODUCT NAME</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="e.g. Royal Black Velvet Blazer Dress">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">PRODUCT CODE</label>
                            <input type="text" name="product_code" value="<?= htmlspecialchars($product['product_code']) ?>" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="C:6900">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">CATEGORY</label>
                            <select name="category_id" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px; background: #fff;">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">REGULAR PRICE (BHD)</label>
                            <input type="number" step="0.01" id="regular_price" name="price" value="<?= $product['price'] ?>" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="45.00">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">SALE PRICE (BHD)</label>
                            <input type="number" step="0.01" id="sale_price" name="sale_price" value="<?= $product['sale_price'] ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="Optional">
                        </div>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">OFFER TAG DISPLAY FORMAT <span id="offer_tag_preview" style="margin-left: 10px; background: #e53e3e; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; display: none;"></span></label>
                        <select id="offer_tag_type" name="offer_tag_type" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px; background: #fff;">
                            <option value="percentage" <?= ($product['offer_tag_type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage Discount (% OFF e.g. 16% OFF)</option>
                            <option value="amount" <?= ($product['offer_tag_type'] ?? '') === 'amount' ? 'selected' : '' ?>>Amount Saved (SAVE BHD e.g. SAVE 10 BHD)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">AVAILABLE COLORS / COMBINATIONS</label>
                        <div id="colorBuilderContainer" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 4px;">
                            <div id="colorRows"></div>
                            <button type="button" onclick="addColorRow()" style="margin-top: 10px; background: #fff; border: 1px dashed #cbd5e0; padding: 6px 12px; font-size: 11px; cursor: pointer; border-radius: 4px; font-weight: 600; color: #4a5568;">+ Add Color Option</button>
                        </div>
                        <input type="hidden" name="colors" id="colorsJsonOutput" value="<?= htmlspecialchars($product['colors'] ?? 'Black, Red, Green & Red, Blue & Gray') ?>">
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

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">AVAILABLE SIZES (Comma Separated)</label>
                        <input type="text" name="sizes" value="<?= htmlspecialchars($product['sizes'] ?? 'S, M, L, XL, XXL') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;">
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">AVAILABLE LENGTHS IN INCHES (Comma Separated)</label>
                        <input type="text" name="lengths" value="<?= htmlspecialchars($product['lengths'] ?? '52, 54, 55, 56, 57, 58, 60') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;">
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">PRIMARY IMAGE</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 11px; margin-bottom: 4px; color: #718096;">Upload New Image File (Overrides URL)</label>
                                <input type="file" name="primary_image_file" accept="image/*" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; margin-bottom: 4px; color: #718096;">Or Image URL</label>
                                <input type="url" name="image" value="<?= htmlspecialchars($product['image']) ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="https://...">
                            </div>
                        </div>
                        <div style="margin-top: 8px;">
                            <span style="font-size: 12px; font-weight: 600; color: #4a5568;">Current Image:</span><br>
                            <img src="<?= htmlspecialchars($product['image']) ?>" style="height: 100px; border-radius: 4px; object-fit: cover; margin-top: 4px; border: 1px solid #cbd5e0;">
                        </div>
                    </div>

                    <div style="margin-bottom: 18px; background: #f8fafc; padding: 20px; border-radius: 4px; border: 1px solid #e2e8f0;">
                        <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--color-primary);">CURRENT GALLERY & MEDIA</label>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px;">
                            <?php 
                            $mediaArr = json_decode($product['media'] ?? '[]', true) ?: [];
                            if (empty($mediaArr)) {
                                echo '<span style="font-size:12px; color:#718096;">No additional media uploaded yet.</span>';
                            }
                            foreach ($mediaArr as $media): 
                                if (($media['type'] ?? '') === 'video') {
                                    echo '<div style="width: 100px; height: 100px; background: #000; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 10px;">VIDEO</div>';
                                } else {
                                    $thumb = $media['thumb'] ?? $media['url'] ?? '';
                                    echo '<img src="'.htmlspecialchars($thumb).'" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e0;">';
                                }
                            endforeach; 
                            ?>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 6px; color: #4a5568;">ADD MORE GALLERY IMAGES</label>
                                <input type="file" name="gallery_images[]" multiple accept="image/*" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                <div style="font-size: 11px; color: #64748b; margin-top: 6px;">New images will be added to the gallery above.</div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 6px; color: #4a5568;">ADD PRODUCT VIDEO</label>
                                <input type="file" name="product_video" accept="video/mp4,video/webm" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                <div style="font-size: 11px; color: #e53e3e; margin-top: 6px; font-weight: 600;">Max Size: 5.5 MB (MP4/WebM)</div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">DESCRIPTION (ENGLISH)</label>
                        <textarea name="description" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px; height: 100px;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">الوصف بالعربي (ARABIC DESCRIPTION - OPTIONAL)</label>
                        <textarea name="description_ar" dir="rtl" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px; height: 100px; font-family: 'Noto Naskh Arabic', 'Arial', sans-serif; font-size: 14px;"><?= htmlspecialchars($product['description_ar'] ?? '') ?></textarea>
                    </div>

                    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="is_featured" value="1" id="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>>
                        <label for="is_featured" style="font-size: 14px;">Show in Home Page Featured Collection</label>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; padding: 14px; border-radius: 4px; font-size: 16px;">Save Changes</button>
                </form>
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
            // Remove trailing .00 if not needed, or keep 2 decimals
            previewSpan.textContent = 'SAVE ' + saved.replace(/\.00$/, '') + ' BHD';
        }
    }

    if (regPriceInput) regPriceInput.addEventListener('input', updatePreview);
    if (salePriceInput) salePriceInput.addEventListener('input', updatePreview);
    if (tagTypeSelect) tagTypeSelect.addEventListener('change', updatePreview);
    
    updatePreview();
});
</script>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProductForm');
    
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = 'Saving...';
            submitBtn.disabled = true;
            
            
            const formData = new FormData(form);
            // Append an ajax flag
            formData.append('ajax', '1');
            
            function showToast(message, isSuccess = true) {
                const toastContainer = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                toast.style.background = isSuccess ? '#c6f6d5' : '#fed7d7';
                toast.style.color = isSuccess ? '#2f855a' : '#c53030';
                toast.style.padding = '12px 20px';
                toast.style.borderRadius = '4px';
                toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                toast.style.fontWeight = 'bold';
                toast.style.fontSize = '14px';
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                toast.style.transition = 'all 0.3s ease';
                toast.innerHTML = message;
                
                toastContainer.appendChild(toast);
                
                // Trigger animation
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                }, 10);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-20px)';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    showToast(data.message || 'Product updated successfully!', true);
                } else {
                    showToast(data.error || 'An error occurred while updating the product.', false);
                }
            })
            .catch(err => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                showToast('A network error occurred. Please try again.', false);
            });
        });
    }
});
</script>


    <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>
</body>

</html>
