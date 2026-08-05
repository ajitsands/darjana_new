<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Products</h1>
                    <p style="color: #718096; font-size: 14px;"><?= $totalProductsCount ?> products in the catalog</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px;">
                <!-- Product List -->
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 16px;">Product Catalog</h3>
                    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
                    <style>
                        #productsTable_wrapper { margin-top: 10px; font-size: 13px; }
                        #productsTable_wrapper .dataTables_filter input { border: 1px solid #cbd5e0; padding: 4px 8px; border-radius: 4px; margin-left: 8px; }
                        #productsTable_wrapper .dataTables_length select { border: 1px solid #cbd5e0; padding: 4px; border-radius: 4px; }
                        table.dataTable thead th { border-bottom: 2px solid #e2e8f0; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.05em; font-family: var(--heading-font-family); padding: 12px; }
                        table.dataTable tbody td { padding: 12px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
                        table.dataTable.no-footer { border-bottom: 1px solid #e2e8f0; }
                    </style>
                    <div class="table-responsive">
                        <table id="productsTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>IMAGE</th>
                                    <th>CODE &amp; NAME</th>
                                    <th>CATEGORY</th>
                                    <th>TAG FORMAT</th>
                                    <th>PRICE</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#productsTable').DataTable({
                                "ajax": "<?= BASE_URL ?>/admin/products/ajax",
                                "processing": true,
                                "pageLength": 20,
                                "ordering": false,
                                "language": {
                                    "search": "Search Products:",
                                    "lengthMenu": "Show _MENU_ products per page"
                                }
                            });
                        });
                    </script>
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

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">اسم المنتج بالعربي (ARABIC PRODUCT NAME - OPTIONAL)</label>
                                <input type="text" name="name_ar" dir="rtl" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-family: 'Noto Naskh Arabic', 'Arial', sans-serif;" placeholder="أدخل اسم المنتج باللغة العربية (اختياري)...">
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">PRODUCT CODE</label>
                                    <input type="text" name="product_code" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="C:6900">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">CATEGORY</label>
                                    <select name="category_id[]" multiple style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; background: #fff; height: 80px;">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div style="font-size: 10.5px; color: #718096; margin-top: 4px;">Hold Ctrl (Win) or Cmd (Mac) to select multiple</div>
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

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">OFFER TAG DISPLAY FORMAT <span id="offer_tag_preview" style="margin-left: 10px; background: #e53e3e; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; display: none;"></span></label>
                                <select id="offer_tag_type" name="offer_tag_type" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px; background: #fff;">
                                    <option value="percentage">Percentage Discount (% OFF e.g. 16% OFF)</option>
                                    <option value="amount">Amount Saved (SAVE BHD e.g. SAVE 10 BHD)</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">AVAILABLE COLORS / COMBINATIONS</label>
                                <div id="colorBuilderContainer" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 4px;">
                                    <div id="colorRows"></div>
                                    <button type="button" onclick="addColorRow()" style="margin-top: 10px; background: #fff; border: 1px dashed #cbd5e0; padding: 6px 12px; font-size: 11px; cursor: pointer; border-radius: 4px; font-weight: 600; color: #4a5568;">+ Add Color Option</button>
                                </div>
                                <input type="hidden" name="colors" id="colorsJsonOutput" value="Black, Red, Green &amp; Red, Blue &amp; Gray, Beige">
                            </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorsJsonOutput = document.getElementById('colorsJsonOutput');
    const colorRowsContainer = document.getElementById('colorRows');
    if(!colorsJsonOutput || !colorRowsContainer) return;
    let initialData = [];
    try {
        let raw = colorsJsonOutput.value;
        if (raw.startsWith('[') || raw.startsWith('{')) { initialData = JSON.parse(raw); }
        else { raw.split(',').map(s => s.trim()).filter(s => s).forEach(p => { initialData.push({ name: p, color1: '#181818' }); }); }
    } catch(e) {}
    if (initialData.length === 0) initialData.push({ name: 'Black', color1: '#181818' });
    initialData.forEach(item => addColorRow(item.name, item.color1, item.color2, item.color3));
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
                if (name) { let obj = { name: name, color1: c1 }; if (count >= 2) obj.color2 = c2; if (count === 3) obj.color3 = c3; result.push(obj); }
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
    div.style.cssText = 'display:flex;gap:8px;align-items:center;margin-bottom:8px;';
    div.innerHTML = `<input type="text" class="c-name" placeholder="Color Name" value="${name}" style="flex:1;padding:6px;border:1px solid #cbd5e0;border-radius:4px;font-size:12px;">
        <select class="c-count" onchange="const v=parseInt(this.value);this.parentElement.querySelector('.c-2').style.display=(v>=2)?'inline-block':'none';this.parentElement.querySelector('.c-3').style.display=(v===3)?'inline-block':'none';" style="padding:6px;border:1px solid #cbd5e0;border-radius:4px;font-size:12px;">
            <option value="1" ${numColors===1?'selected':''}>1 Color</option><option value="2" ${numColors===2?'selected':''}>2 Colors</option><option value="3" ${numColors===3?'selected':''}>3 Colors</option>
        </select>
        <input type="color" class="c-1" value="${c1}" style="width:32px;height:32px;padding:0;border:none;cursor:pointer;">
        <input type="color" class="c-2" value="${c2||'#ffffff'}" style="width:32px;height:32px;padding:0;border:none;cursor:pointer;display:${numColors>=2?'inline-block':'none'};">
        <input type="color" class="c-3" value="${c3||'#ffffff'}" style="width:32px;height:32px;padding:0;border:none;cursor:pointer;display:${numColors===3?'inline-block':'none'};">
        <button type="button" onclick="this.parentElement.remove()" style="background:#fed7d7;border:none;color:#c53030;cursor:pointer;padding:6px 10px;border-radius:4px;font-size:11px;font-weight:bold;">X</button>`;
    container.appendChild(div);
}
</script>

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">AVAILABLE SIZES (Comma Separated)</label>
                                <input type="text" name="sizes" value="S, M, L, XL, XXL" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="e.g. S, M, L, XL, XXL">
                            </div>

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">AVAILABLE LENGTHS IN INCHES (Comma Separated)</label>
                                <input type="text" name="lengths" value="52, 54, 55, 56, 57, 58, 60" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="e.g. 52, 54, 55, 56, 57, 58, 60">
                            </div>

                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">PRIMARY IMAGE</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; margin-bottom: 4px; color: #718096;">Upload Image File</label>
                                        <input type="file" name="primary_image_file" accept="image/*" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; margin-bottom: 4px; color: #718096;">Or Image URL</label>
                                        <input type="url" name="image" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 4px;" placeholder="https://...">
                                    </div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; background: #f8fafc; padding: 16px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--color-primary);">UPLOAD GALLERY IMAGES</label>
                                    <input type="file" name="gallery_images[]" multiple accept="image/*" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                    <div style="font-size: 10.5px; color: #64748b; margin-top: 4px;">Select multiple images for the gallery.</div>
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
                            </div>

                            <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 20px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="is_featured" value="1" id="is_featured" checked>
                                    <label for="is_featured" style="font-size: 13px;">Show in Home Page Featured Collection</label>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="is_active" value="1" id="is_active" checked>
                                    <label for="is_active" style="font-size: 13px; font-weight: bold; color: var(--color-primary);">Display on Website (Product Available)</label>
                                </div>
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
        if (isNaN(regPrice) || isNaN(salePrice) || salePrice >= regPrice || salePrice <= 0) { previewSpan.style.display = 'none'; return; }
        previewSpan.style.display = 'inline-block';
        if (tagType === 'percentage') { previewSpan.textContent = Math.round(((regPrice - salePrice) / regPrice) * 100) + '% OFF'; }
        else { previewSpan.textContent = 'SAVE ' + (regPrice - salePrice).toFixed(2).replace(/\.00$/, '') + ' BHD'; }
    }
    if (regPriceInput) regPriceInput.addEventListener('input', updatePreview);
    if (salePriceInput) salePriceInput.addEventListener('input', updatePreview);
    if (tagTypeSelect) tagTypeSelect.addEventListener('change', updatePreview);
    updatePreview();
});
</script>
</body>
</html>
