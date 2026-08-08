<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Edit Category</h1>
                    <p style="color: #718096; font-size: 14px;">Update details for <?= htmlspecialchars($category['name']) ?></p>
                </div>
                <a href="<?= BASE_URL ?>/admin/categories" style="color: #4a5568; text-decoration: none; font-size: 14px; font-weight: 600;">← Back to Categories</a>
            </div>

            <div style="background: #fff; padding: 30px; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px;">
                <form action="<?= BASE_URL ?>/admin/category/edit/<?= $category['id'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>CATEGORY NAME</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>CATEGORY SLUG</label>
                        <input type="text" name="slug" value="<?= htmlspecialchars($category['slug']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>DESCRIPTION</label>
                        <textarea name="description" rows="4"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <label style="font-weight: 700; color: #1e293b;">CATEGORY IMAGE</label>
                        <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 15px;">
                            <div>
                                <span style="font-size: 11px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Current Image:</span>
                                <?php if ($category['image']): ?>
                                    <img src="<?= htmlspecialchars($category['image']) ?>" style="width: 110px; height: 110px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                <?php else: ?>
                                    <div style="width: 110px; height: 110px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #a0aec0; font-size: 12px;">No Image</div>
                                <?php endif; ?>
                            </div>
                            
                            <div style="flex: 1;">
                                <div style="margin-bottom: 10px;">
                                    <label style="font-size: 12px; font-weight: normal; margin-bottom: 4px; display: block; color: #475569;">Upload New File (Overrides existing image)</label>
                                    <input type="file" name="image_file" accept="image/*" onchange="handleCatImageFileChange(this)" style="padding: 8px; width: 100%; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                </div>
                                <div>
                                    <label style="font-size: 12px; font-weight: normal; margin-bottom: 4px; display: block; color: #475569;">Or update Image URL</label>
                                    <input type="url" name="image" value="<?= htmlspecialchars($category['image']) ?>" oninput="handleCatImageUrlChange(this)" placeholder="https://..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px;">
                                </div>
                                <div id="catImagePreviewBox" style="display:none;"></div>
                                <div id="catUrlPreviewBox" style="display:none;"></div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 14px 30px; font-size: 15px;">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

<script>
function handleCatImageFileChange(input) {
    const box = document.getElementById('catImagePreviewBox');
    if (!box) return;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            box.innerHTML = `
                <div style="display:flex; align-items:center; gap:12px; background:#f0fdf4; border:1px solid #bbf7d0; padding:8px 12px; border-radius:6px; margin-top:10px;">
                    <img src="${e.target.result}" style="width:60px; height:60px; object-fit:cover; border-radius:4px; border:1px solid #cbd5e0;">
                    <div style="flex:1; overflow:hidden;">
                        <div style="font-size:11px; font-weight:700; color:#16a34a; text-transform:uppercase;">NEW IMAGE SELECTED</div>
                        <div style="font-size:12px; font-weight:600; color:#1e293b; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${file.name}</div>
                        <div style="font-size:11px; color:#64748b;">${(file.size/1024).toFixed(1)} KB</div>
                    </div>
                    <button type="button" onclick="clearCatImageFile()" style="background:#fee2e2; border:1px solid #fca5a5; color:#ef4444; border-radius:4px; padding:4px 8px; font-size:11px; font-weight:700; cursor:pointer;">❌ Delete</button>
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

function clearCatImageFile() {
    const input = document.querySelector('input[name="image_file"]');
    if (input) input.value = '';
    const box = document.getElementById('catImagePreviewBox');
    if (box) { box.style.display = 'none'; box.innerHTML = ''; }
}

function handleCatImageUrlChange(input) {
    const box = document.getElementById('catUrlPreviewBox');
    if (!box) return;
    const val = input.value.trim();
    if (val && (val.startsWith('http://') || val.startsWith('https://'))) {
        box.innerHTML = `
            <div style="display:flex; align-items:center; gap:12px; background:#f8fafc; border:1px solid #e2e8f0; padding:8px 12px; border-radius:6px; margin-top:10px;">
                <img src="${val}" onerror="this.src='https://via.placeholder.com/60?text=Error';" style="width:60px; height:60px; object-fit:cover; border-radius:4px; border:1px solid #cbd5e0;">
                <div style="flex:1; overflow:hidden;">
                    <div style="font-size:11px; font-weight:700; color:#3b82f6; text-transform:uppercase;">IMAGE URL PREVIEW</div>
                    <div style="font-size:11.5px; color:#64748b; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${val}</div>
                </div>
                <button type="button" onclick="clearCatImageUrl()" style="background:#fee2e2; border:1px solid #fca5a5; color:#ef4444; border-radius:4px; padding:4px 8px; font-size:11px; font-weight:700; cursor:pointer;">❌ Clear</button>
            </div>
        `;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
        box.innerHTML = '';
    }
}

function clearCatImageUrl() {
    const input = document.querySelector('input[name="image"]');
    if (input) input.value = '';
    const box = document.getElementById('catUrlPreviewBox');
    if (box) { box.style.display = 'none'; box.innerHTML = ''; }
}
</script>
</body>
</html>
