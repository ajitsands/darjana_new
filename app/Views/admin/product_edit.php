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

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">اسم المنتج بالعربي (ARABIC PRODUCT NAME - OPTIONAL)</label>
                        <input type="text" name="name_ar" value="<?= htmlspecialchars($product['name_ar'] ?? '') ?>" dir="rtl" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px; font-family: 'Noto Naskh Arabic', 'Arial', sans-serif;" placeholder="أدخل اسم المنتج باللغة العربية (اختياري)...">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">PRODUCT CODE</label>
                            <input type="text" name="product_code" value="<?= htmlspecialchars($product['product_code']) ?>" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="C:6900">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">CATEGORY (MULTI-SELECT)</label>
                            <select name="category_id[]" class="select2-category" multiple style="width: 100%;">
                                <?php 
                                $selectedCats = explode(',', $product['category_id'] ?? '');
                                foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= in_array($cat['id'], $selectedCats) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div style="font-size: 10.5px; color: #718096; margin-top: 4px;">Search and click to select multiple categories</div>
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
                        <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #1e293b;">PRIMARY PRODUCT IMAGE</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 11px; margin-bottom: 4px; color: #718096;">Upload New Image File (Overrides URL)</label>
                                <input type="file" id="primaryImageInput" name="primary_image_file" accept="image/*" onchange="handlePrimaryImageChange(this)" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; margin-bottom: 4px; color: #718096;">Or Image URL</label>
                                <input type="url" id="primaryUrlInput" name="image" value="<?= htmlspecialchars($product['image']) ?>" oninput="handlePrimaryUrlChange(this)" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="https://...">
                            </div>
                        </div>
                        <div id="primaryImagePreviewBox" style="display:none; margin-top: 8px;"></div>
                        <div id="primaryUrlPreviewBox" style="display:none; margin-top: 8px;"></div>
                        <div style="margin-top: 8px;">
                            <span style="font-size: 12px; font-weight: 600; color: #4a5568;">Current Primary Image:</span><br>
                            <img src="<?= htmlspecialchars($product['image']) ?>" style="height: 90px; border-radius: 4px; object-fit: cover; margin-top: 4px; border: 1px solid #cbd5e0;">
                        </div>
                    </div>

                    <div style="margin-bottom: 18px; background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #0f172a;">CURRENT GALLERY &amp; MEDIA</label>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px;">
                            <?php 
                            $mediaArr = json_decode($product['media'] ?? '[]', true) ?: [];
                            if (empty($mediaArr)) {
                                echo '<span style="font-size:12px; color:#718096;">No additional media uploaded yet.</span>';
                            }
                            foreach ($mediaArr as $index => $media): 
                                echo '<div style="position: relative; display: inline-block;">';
                                if (($media['type'] ?? '') === 'video') {
                                    echo '<div style="width: 100px; height: 100px; background: #000; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 10px;">VIDEO</div>';
                                } else {
                                    $thumb = $media['thumb'] ?? $media['url'] ?? '';
                                    echo '<img src="'.htmlspecialchars($thumb).'" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e0;">';
                                }
                                // Delete button
                                echo '<a href="#" onclick="confirmDelete(event, \''.BASE_URL.'/admin/product/delete-media/'.$product['id'].'?index='.$index.'\', \'Are you sure you want to delete this media? This will permanently remove the file.\')" style="position: absolute; top: -6px; right: -6px; background: #e53e3e; color: #fff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px; font-weight: bold; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">&times;</a>';
                                echo '</div>';
                            endforeach; 
                            ?>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 6px; color: #4a5568;">ADD MORE GALLERY IMAGES</label>
                                <input type="file" id="galleryInput" name="gallery_images[]" multiple accept="image/*" onchange="handleGalleryImagesChange(this)" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                <div style="font-size: 11px; color: #64748b; margin-top: 6px;">New images will be added to the gallery above. Preview below:</div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 6px; color: #4a5568;">ADD PRODUCT VIDEO</label>
                                <input type="file" id="videoInput" name="product_video" accept="video/mp4,video/webm" onchange="handleVideoChange(this)" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                <div style="font-size: 11px; color: #e53e3e; margin-top: 6px; font-weight: 600;">Max Size: 5.5 MB (MP4/WebM)</div>
                            </div>
                        </div>

                        <!-- Live Gallery & Video Preview Container -->
                        <div id="galleryPreviewContainer" style="display:none; margin-top: 14px;"></div>
                        <div id="videoPreviewBox" style="display:none; margin-top: 14px;"></div>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">DESCRIPTION (ENGLISH)</label>
                        <textarea name="description" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px; height: 100px;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">الوصف بالعربي (ARABIC DESCRIPTION - OPTIONAL)</label>
                        <textarea name="description_ar" dir="rtl" style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 4px; height: 100px; font-family: 'Noto Naskh Arabic', 'Arial', sans-serif; font-size: 14px;"><?= htmlspecialchars($product['description_ar'] ?? '') ?></textarea>
                    </div>

                    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 20px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="is_featured" value="1" id="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>>
                            <label for="is_featured" style="font-size: 14px;">Show in Home Page Featured Collection</label>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="is_active" value="1" id="is_active" <?= (isset($product['is_active']) && $product['is_active']) ? 'checked' : '' ?>>
                            <label for="is_active" style="font-size: 14px; font-weight: bold; color: var(--color-primary);">Display on Website (Product Available)</label>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="is_verified" value="1" id="is_verified" <?= (!isset($product['is_verified']) || $product['is_verified']) ? 'checked' : '' ?>>
                            <label for="is_verified" style="font-size: 14px; font-weight: 700; color: #16a34a;">🟢 Verified &amp; Published to Storefront Portal</label>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="button" onclick="openImageVerificationModal()" style="flex: 1; background: #3b82f6; color: #ffffff; border: none; padding: 14px; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 6px rgba(59,130,246,0.3);">
                            🔍 Verify All Images
                        </button>
                        <button type="submit" class="btn-primary" style="flex: 1; padding: 14px; border-radius: 6px; font-size: 14px; font-weight: 700; background: #181818;">Save Changes</button>
                    </div>
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

        if (tagType === 'percentage') {
            const percent = Math.round(((regPrice - salePrice) / regPrice) * 100);
            if (percent <= 0) {
                previewSpan.style.display = 'none';
                return;
            }
            previewSpan.style.display = 'inline-block';
            previewSpan.textContent = percent + '% OFF';
        } else {
            const savedVal = regPrice - salePrice;
            if (savedVal <= 0) {
                previewSpan.style.display = 'none';
                return;
            }
            previewSpan.style.display = 'inline-block';
            const saved = savedVal.toFixed(2);
            previewSpan.textContent = 'SAVE ' + saved.replace(/\.00$/, '') + ' BHD';
        }
    }

    if (regPriceInput) regPriceInput.addEventListener('input', updatePreview);
    if (salePriceInput) salePriceInput.addEventListener('input', updatePreview);
    if (tagTypeSelect) tagTypeSelect.addEventListener('change', updatePreview);
    
    updatePreview();

    $('.select2-category').select2({
        placeholder: '🔍 Search & Select Categories...',
        allowClear: true,
        width: '100%'
    });
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

<!-- ===== INTERACTIVE PRODUCT IMAGE PREVIEW, DELETE & VERIFICATION SYSTEM ===== -->
<script>
let galleryFilesDT = new DataTransfer();

function handlePrimaryImageChange(input) {
    const box = document.getElementById('primaryImagePreviewBox');
    if (!box) return;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            box.innerHTML = `
                <div style="display:flex; align-items:center; gap:12px; background:#f0fdf4; border:1px solid #bbf7d0; padding:8px 12px; border-radius:6px;">
                    <img src="${e.target.result}" style="width:52px; height:52px; object-fit:cover; border-radius:4px; border:1px solid #cbd5e0;">
                    <div style="flex:1; overflow:hidden;">
                        <div style="font-size:11px; font-weight:700; color:#16a34a; text-transform:uppercase;">NEW PRIMARY PHOTO SELECTED</div>
                        <div style="font-size:12px; font-weight:600; color:#1e293b; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${file.name}</div>
                        <div style="font-size:11px; color:#64748b;">${(file.size/1024).toFixed(1)} KB</div>
                    </div>
                    <button type="button" onclick="clearPrimaryImageFile()" style="background:#fee2e2; border:1px solid #fca5a5; color:#ef4444; border-radius:4px; padding:4px 8px; font-size:11px; font-weight:700; cursor:pointer;">❌ Delete</button>
                </div>
            `;
            box.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        box.style.display = 'none';
        box.innerHTML = '';
    }
}

function clearPrimaryImageFile() {
    const input = document.getElementById('primaryImageInput');
    if (input) input.value = '';
    const box = document.getElementById('primaryImagePreviewBox');
    if (box) { box.style.display = 'none'; box.innerHTML = ''; }
}

function handlePrimaryUrlChange(input) {
    const box = document.getElementById('primaryUrlPreviewBox');
    if (!box) return;
    const val = input.value.trim();
    if (val && (val.startsWith('http://') || val.startsWith('https://'))) {
        box.innerHTML = `
            <div style="display:flex; align-items:center; gap:12px; background:#f8fafc; border:1px solid #e2e8f0; padding:8px 12px; border-radius:6px;">
                <img src="${val}" onerror="this.src='https://via.placeholder.com/52?text=Error';" style="width:52px; height:52px; object-fit:cover; border-radius:4px; border:1px solid #cbd5e0;">
                <div style="flex:1; overflow:hidden;">
                    <div style="font-size:11px; font-weight:700; color:#3b82f6; text-transform:uppercase;">IMAGE URL PREVIEW</div>
                    <div style="font-size:11.5px; color:#64748b; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${val}</div>
                </div>
                <button type="button" onclick="clearPrimaryUrl()" style="background:#fee2e2; border:1px solid #fca5a5; color:#ef4444; border-radius:4px; padding:4px 8px; font-size:11px; font-weight:700; cursor:pointer;">❌ Clear</button>
            </div>
        `;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
        box.innerHTML = '';
    }
}

function clearPrimaryUrl() {
    const input = document.getElementById('primaryUrlInput');
    if (input) input.value = '';
    const box = document.getElementById('primaryUrlPreviewBox');
    if (box) { box.style.display = 'none'; box.innerHTML = ''; }
}

function handleGalleryImagesChange(input) {
    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            galleryFilesDT.items.add(input.files[i]);
        }
        input.files = galleryFilesDT.files;
    }
    renderGalleryPreviews();
}

function removeGalleryFile(index) {
    const newDT = new DataTransfer();
    const { files } = galleryFilesDT;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) newDT.items.add(files[i]);
    }
    galleryFilesDT = newDT;
    const input = document.getElementById('galleryInput');
    if (input) input.files = galleryFilesDT.files;
    renderGalleryPreviews();
}

function renderGalleryPreviews() {
    const container = document.getElementById('galleryPreviewContainer');
    if (!container) return;
    container.innerHTML = '';
    const files = galleryFilesDT.files;
    if (files.length === 0) {
        container.style.display = 'none';
        return;
    }
    container.style.display = 'grid';
    container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(95px, 1fr))';
    container.style.gap = '10px';

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const card = document.createElement('div');
        card.style.cssText = 'position:relative; background:#ffffff; border:1px solid #cbd5e0; border-radius:6px; padding:6px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.03);';
        
        const reader = new FileReader();
        reader.onload = (function(idx, fname, fsize) {
            return function(e) {
                card.innerHTML = `
                    <div style="position:relative;">
                        <img src="${e.target.result}" style="width:100%; height:75px; object-fit:cover; border-radius:4px;">
                        <button type="button" onclick="removeGalleryFile(${idx})" style="position:absolute; top:-6px; right:-6px; background:#ef4444; color:#ffffff; border:2px solid #ffffff; border-radius:50%; width:22px; height:22px; font-size:12px; font-weight:bold; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 4px rgba(0,0,0,0.2);" title="Delete Image">✕</button>
                    </div>
                    <div style="font-size:10px; font-weight:700; color:#3b82f6; margin-top:4px;">New Gallery #${idx + 1}</div>
                    <div style="font-size:9.5px; color:#64748b; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${fname}</div>
                    <div style="font-size:9px; color:#94a3b8;">${(fsize/1024).toFixed(0)} KB</div>
                `;
            };
        })(i, file.name, file.size);
        reader.readAsDataURL(file);
        container.appendChild(card);
    }
}

function handleVideoChange(input) {
    const box = document.getElementById('videoPreviewBox');
    if (!box) return;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const videoUrl = URL.createObjectURL(file);
        box.innerHTML = `
            <div style="display:flex; align-items:center; gap:12px; background:#fff5f5; border:1px solid #feb2b2; padding:8px 12px; border-radius:6px;">
                <video src="${videoUrl}" controls style="width:80px; height:55px; object-fit:cover; border-radius:4px; background:#000;"></video>
                <div style="flex:1; overflow:hidden;">
                    <div style="font-size:11px; font-weight:700; color:#e53e3e; text-transform:uppercase;">PRODUCT VIDEO SELECTED</div>
                    <div style="font-size:12px; font-weight:600; color:#1e293b; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${file.name}</div>
                    <div style="font-size:11px; color:#64748b;">${(file.size/(1024*1024)).toFixed(2)} MB</div>
                </div>
                <button type="button" onclick="clearVideoFile()" style="background:#fee2e2; border:1px solid #fca5a5; color:#ef4444; border-radius:4px; padding:4px 8px; font-size:11px; font-weight:700; cursor:pointer;">❌ Delete</button>
            </div>
        `;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
        box.innerHTML = '';
    }
}

function clearVideoFile() {
    const input = document.getElementById('videoInput');
    if (input) input.value = '';
    const box = document.getElementById('videoPreviewBox');
    if (box) { box.style.display = 'none'; box.innerHTML = ''; }
}

function openImageVerificationModal() {
    const grid = document.getElementById('verifyModalGrid');
    if (!grid) return;
    grid.innerHTML = '';

    let mediaItems = [];

    const primaryInput = document.getElementById('primaryImageInput');
    const primaryUrlInput = document.getElementById('primaryUrlInput');

    if (primaryInput && primaryInput.files && primaryInput.files[0]) {
        const file = primaryInput.files[0];
        mediaItems.push({
            type: 'file',
            file: file,
            badge: 'NEW PRIMARY PHOTO',
            badgeBg: '#16a34a',
            name: file.name,
            size: (file.size / 1024).toFixed(1) + ' KB'
        });
    } else if (primaryUrlInput && primaryUrlInput.value.trim()) {
        const url = primaryUrlInput.value.trim();
        mediaItems.push({
            type: 'url',
            url: url,
            badge: 'PRIMARY PHOTO',
            badgeBg: '#181818',
            name: 'Primary Image',
            size: 'Current Image'
        });
    }

    if (galleryFilesDT && galleryFilesDT.files) {
        for (let i = 0; i < galleryFilesDT.files.length; i++) {
            const f = galleryFilesDT.files[i];
            mediaItems.push({
                type: 'file',
                file: f,
                badge: `NEW GALLERY #${i + 1}`,
                badgeBg: '#3b82f6',
                name: f.name,
                size: (f.size / 1024).toFixed(1) + ' KB'
            });
        }
    }

    const videoInput = document.getElementById('videoInput');
    if (videoInput && videoInput.files && videoInput.files[0]) {
        const vf = videoInput.files[0];
        mediaItems.push({
            type: 'video_file',
            file: vf,
            badge: 'PRODUCT VIDEO',
            badgeBg: '#e53e3e',
            name: vf.name,
            size: (vf.size / (1024 * 1024)).toFixed(2) + ' MB'
        });
    }

    if (mediaItems.length === 0) {
        Swal.fire({
            title: 'No Images Available',
            text: 'No product images available for verification.',
            icon: 'warning',
            confirmButtonColor: '#181818'
        });
        return;
    }

    mediaItems.forEach((item) => {
        const card = document.createElement('div');
        card.className = 'img-verify-card';

        if (item.type === 'file') {
            const reader = new FileReader();
            reader.onload = function(e) {
                card.innerHTML = `
                    <div style="position:relative; height:180px; background:#000;">
                        <span class="img-verify-badge" style="background:${item.badgeBg};">${item.badge}</span>
                        <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div style="padding:10px; text-align:left;">
                        <div style="font-size:12px; font-weight:700; color:#0f172a; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${item.name}</div>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">Size: ${item.size}</div>
                    </div>
                `;
            };
            reader.readAsDataURL(item.file);
        } else if (item.type === 'url') {
            card.innerHTML = `
                <div style="position:relative; height:180px; background:#000;">
                    <span class="img-verify-badge" style="background:${item.badgeBg};">${item.badge}</span>
                    <img src="${item.url}" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div style="padding:10px; text-align:left;">
                    <div style="font-size:12px; font-weight:700; color:#0f172a;">${item.name}</div>
                    <div style="font-size:11px; color:#64748b; margin-top:2px;">Current Image</div>
                </div>
            `;
        } else if (item.type === 'video_file') {
            const videoUrl = URL.createObjectURL(item.file);
            card.innerHTML = `
                <div style="position:relative; height:180px; background:#000;">
                    <span class="img-verify-badge" style="background:${item.badgeBg};">${item.badge}</span>
                    <video src="${videoUrl}" controls style="width:100%; height:100%; object-fit:cover;"></video>
                </div>
                <div style="padding:10px; text-align:left;">
                    <div style="font-size:12px; font-weight:700; color:#0f172a; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${item.name}</div>
                    <div style="font-size:11px; color:#e53e3e; margin-top:2px; font-weight:600;">Video Size: ${item.size}</div>
                </div>
            `;
        }
        grid.appendChild(card);
    });

    document.getElementById('verifyImagesModal').style.display = 'flex';
}

function closeImageVerificationModal() {
    const modal = document.getElementById('verifyImagesModal');
    if (modal) modal.style.display = 'none';
}

function submitProductFormAfterVerify() {
    closeImageVerificationModal();
    const form = document.getElementById('editProductForm');
    if (form) {
        form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
    }
}
</script>

<!-- ===== PRODUCT IMAGE VERIFICATION & INSPECTION MODAL ===== -->
<style>
.img-verify-modal-overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.75); z-index: 999999; backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.img-verify-modal-card {
    background: #ffffff; border-radius: 12px; width: 92%; max-width: 900px;
    max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    display: flex; flex-direction: column;
}
.img-verify-modal-header {
    padding: 20px 24px; background: #181818; color: #ffffff;
    display: flex; justify-content: space-between; align-items: center; border-radius: 12px 12px 0 0;
}
.img-verify-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px; padding: 24px;
}
.img-verify-card {
    border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;
    background: #f8fafc; text-align: center; position: relative; box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.img-verify-badge {
    position: absolute; top: 8px; left: 8px; background: rgba(0,0,0,0.75); color: #fff;
    font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em; z-index: 10;
}
</style>

<div class="img-verify-modal-overlay" id="verifyImagesModal">
    <div class="img-verify-modal-card">
        <div class="img-verify-modal-header">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
                🔍 Product Image Verification &amp; Inspection
            </h3>
            <button type="button" onclick="closeImageVerificationModal()" style="background: none; border: none; color: #ffffff; font-size: 24px; cursor: pointer;">✕</button>
        </div>
        <div style="padding: 16px 24px 0; font-size: 13px; color: #64748b;">
            Verify all dress/abaya photos before updating. Review clarity, order, and details below:
        </div>
        <div class="img-verify-grid" id="verifyModalGrid">
            <!-- Populated via JS -->
        </div>
        <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 12px 12px;">
            <button type="button" onclick="closeImageVerificationModal()" style="background: #ffffff; border: 1px solid #cbd5e0; color: #475569; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer;">
                ✏️ Edit &amp; Change Photos
            </button>
            <button type="button" onclick="submitProductFormAfterVerify()" style="background: #16a34a; color: #ffffff; border: none; padding: 12px 28px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(22,163,74,0.25);">
                ✅ All Images Verified — Save Changes Now
            </button>
        </div>
    </div>
</div>
</body>
</html>
