<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Products</h1>
                    <p style="color: #718096; font-size: 14px;"><?= $totalProductsCount ?> products in the catalog</p>
                </div>
            </div>

            
            <?php if (isset($_GET['success'])): ?>
                <div id="toast" style="background-color: #38a169; color: white; padding: 15px 25px; border-radius: 4px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-weight: 600;">
                    Product Added Successfully!
                </div>
                <script>setTimeout(() => { document.getElementById('toast').style.display = 'none'; }, 4000);</script>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div id="errorToast" style="background-color: #e53e3e; color: white; padding: 15px 25px; border-radius: 4px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-weight: 600;">
                    <?php 
                        if ($_GET['error'] === 'file_too_large') echo "Error: Uploaded files exceed the server limit (post_max_size). Try fewer images.";
                        else if ($_GET['error'] === 'missing_fields') echo "Error: Please fill out all required fields.";
                        else echo "An error occurred.";
                    ?>
                </div>
                <script>setTimeout(() => { document.getElementById('errorToast').style.display = 'none'; }, 6000);</script>
            <?php endif; ?>
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

                            <button type="submit" id="publishBtn" class="btn-primary" style="width: 100%; padding: 12px; border-radius: 4px;" onclick="this.innerHTML='<span style=\'display:inline-block;animation:spin 1s linear infinite;\'>⏳</span> Uploading... Please Wait'; this.style.opacity='0.7'; this.style.pointerEvents='none'; this.form.submit();">Publish Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- SHARE MODAL -->
<div id="shareModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: #fff; width: 90%; max-width: 520px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden; animation: fadeIn 0.2s ease;">
        <div style="padding: 18px 24px; background: #1a1a1a; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span id="shareModalCode" style="font-size: 11px; color: #c5a059; font-weight: 700; display: block; letter-spacing: 0.05em;">PRODUCT CODE</span>
                <h3 id="shareModalName" style="margin: 0; font-size: 16px; font-weight: 600; color: #fff;">Share Product Link</h3>
            </div>
            <button type="button" onclick="closeShareModal()" style="background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        
        <div style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <span style="font-size: 12px; font-weight: 700; color: #4a5568; text-transform: uppercase; letter-spacing: 0.05em;">CHOOSE PLATFORM TO COPY LINK</span>
                <span id="shareModalTotal" style="font-size: 12px; font-weight: 700; color: #2b6cb0; background: #ebf8ff; padding: 2px 8px; border-radius: 12px; border: 1px solid #bee3f8;">Total Clicks: 0</span>
            </div>

            <!-- Platform Buttons Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
                <!-- Instagram -->
                <button type="button" onclick="copySharePlatform('instagram')" class="share-platform-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; cursor: pointer; transition: all 0.2s; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 18px;">📸</span>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: #2d3748;">Instagram</div>
                            <div style="font-size: 10px; color: #a0aec0;">Copy link</div>
                        </div>
                    </div>
                    <span id="stat_instagram" style="font-size: 11px; font-weight: 700; color: #d69e2e; background: #fefcbf; padding: 2px 6px; border-radius: 10px;">0</span>
                </button>

                <!-- Facebook -->
                <button type="button" onclick="copySharePlatform('facebook')" class="share-platform-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; cursor: pointer; transition: all 0.2s; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 18px;">📘</span>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: #2d3748;">Facebook</div>
                            <div style="font-size: 10px; color: #a0aec0;">Copy link</div>
                        </div>
                    </div>
                    <span id="stat_facebook" style="font-size: 11px; font-weight: 700; color: #3182ce; background: #ebf8ff; padding: 2px 6px; border-radius: 10px;">0</span>
                </button>

                <!-- WhatsApp -->
                <button type="button" onclick="copySharePlatform('whatsapp')" class="share-platform-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; cursor: pointer; transition: all 0.2s; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 18px;">💬</span>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: #2d3748;">WhatsApp</div>
                            <div style="font-size: 10px; color: #a0aec0;">Copy link</div>
                        </div>
                    </div>
                    <span id="stat_whatsapp" style="font-size: 11px; font-weight: 700; color: #38a169; background: #f0fff4; padding: 2px 6px; border-radius: 10px;">0</span>
                </button>

                <!-- TikTok -->
                <button type="button" onclick="copySharePlatform('tiktok')" class="share-platform-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; cursor: pointer; transition: all 0.2s; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 18px;">🎵</span>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: #2d3748;">TikTok</div>
                            <div style="font-size: 10px; color: #a0aec0;">Copy link</div>
                        </div>
                    </div>
                    <span id="stat_tiktok" style="font-size: 11px; font-weight: 700; color: #805ad5; background: #faf5ff; padding: 2px 6px; border-radius: 10px;">0</span>
                </button>

                <!-- YouTube (Spans full width) -->
                <button type="button" onclick="copySharePlatform('youtube')" class="share-platform-btn" style="grid-column: span 2; display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; cursor: pointer; transition: all 0.2s; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 18px;">📺</span>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: #2d3748;">YouTube</div>
                            <div style="font-size: 10px; color: #a0aec0;">Copy link</div>
                        </div>
                    </div>
                    <span id="stat_youtube" style="font-size: 11px; font-weight: 700; color: #e53e3e; background: #fff5f5; padding: 2px 6px; border-radius: 10px;">0</span>
                </button>
            </div>

            <!-- Direct URL Preview Box -->
            <div style="background: #f7fafc; padding: 12px; border-radius: 6px; border: 1px solid #edf2f7;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #718096; margin-bottom: 6px;">GENERATED TRACKABLE LINK</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="shareUrlPreview" readonly style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 12px; font-family: monospace; background: #fff; color: #2d3748;">
                    <button type="button" onclick="copyCurrentShareUrl()" style="background: #2b6cb0; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">Copy Link</button>
                </div>
            </div>
            
            <div style="margin-top: 14px; font-size: 11px; color: #a0aec0; text-align: center;">
                ⏱️ Click deduplication duration: Configurable in <a href="<?= BASE_URL ?>/admin/settings" style="color: #2b6cb0; text-decoration: underline;">Store Settings</a> (Default: 1 Hour).
            </div>
        </div>
    </div>
</div>

<!-- SHARE TOAST NOTIFICATION -->
<div id="shareToast" style="display: none; position: fixed; bottom: 30px; right: 30px; background: #2b6cb0; color: #fff; padding: 12px 20px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-weight: 600; font-size: 13px; z-index: 100000; align-items: center; gap: 8px;">
    <span>✅</span> <span id="shareToastText">Link copied to clipboard!</span>
</div>

<style>
.share-platform-btn:hover { border-color: #2b6cb0 !important; background: #f7fafc !important; transform: translateY(-1px); }
</style>

<script>
let currentShareProduct = null;

function openShareModal(product) {
    currentShareProduct = product;
    document.getElementById('shareModalCode').textContent = product.code || 'PRODUCT';
    document.getElementById('shareModalName').textContent = product.name || 'Share Product';
    document.getElementById('shareModalTotal').textContent = 'Total Clicks: ' + (product.total || 0);

    const stats = product.stats || {};
    document.getElementById('stat_instagram').textContent = stats.instagram || 0;
    document.getElementById('stat_facebook').textContent = stats.facebook || 0;
    document.getElementById('stat_whatsapp').textContent = stats.whatsapp || 0;
    document.getElementById('stat_tiktok').textContent = stats.tiktok || 0;
    document.getElementById('stat_youtube').textContent = stats.youtube || 0;

    // Default to whatsapp link preview
    updateShareUrlPreview('whatsapp');

    const modal = document.getElementById('shareModal');
    modal.style.display = 'flex';
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}

function updateShareUrlPreview(source) {
    if (!currentShareProduct) return;
    const url = currentShareProduct.url + '?source=' + source;
    document.getElementById('shareUrlPreview').value = url;
    return url;
}

function copySharePlatform(source) {
    const url = updateShareUrlPreview(source);
    if (!url) return;

    copyToClipboard(url, 'Copied ' + capitalize(source) + ' share link!');
}

function copyCurrentShareUrl() {
    const urlInput = document.getElementById('shareUrlPreview');
    if (!urlInput || !urlInput.value) return;

    copyToClipboard(urlInput.value, 'Share link copied to clipboard!');
}

function copyToClipboard(text, message) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showShareToast(message);
        }).catch(err => {
            fallbackCopyText(text, message);
        });
    } else {
        fallbackCopyText(text, message);
    }
}

function fallbackCopyText(text, message) {
    const input = document.getElementById('shareUrlPreview');
    input.select();
    input.setSelectionRange(0, 99999);
    try {
        document.execCommand('copy');
        showShareToast(message);
    } catch (e) {
        alert(message + '\n' + text);
    }
}

function showShareToast(message) {
    const toast = document.getElementById('shareToast');
    const toastText = document.getElementById('shareToastText');
    if (!toast || !toastText) return;

    toastText.textContent = message;
    toast.style.display = 'flex';
    setTimeout(() => {
        toast.style.display = 'none';
    }, 3500);
}

function capitalize(str) {
    if (!str) return '';
    if (str === 'whatsapp') return 'WhatsApp';
    if (str === 'tiktok') return 'TikTok';
    if (str === 'youtube') return 'YouTube';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Close modal on background click
window.addEventListener('click', function(e) {
    const modal = document.getElementById('shareModal');
    if (e.target === modal) {
        closeShareModal();
    }
});

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
        
        if (tagType === 'percentage') {
            const percent = Math.round(((regPrice - salePrice) / regPrice) * 100);
            if (percent <= 0) { previewSpan.style.display = 'none'; return; }
            previewSpan.style.display = 'inline-block';
            previewSpan.textContent = percent + '% OFF';
        } else {
            const savedVal = regPrice - salePrice;
            if (savedVal <= 0) { previewSpan.style.display = 'none'; return; }
            previewSpan.style.display = 'inline-block';
            previewSpan.textContent = 'SAVE ' + savedVal.toFixed(2).replace(/\.00$/, '') + ' BHD';
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
