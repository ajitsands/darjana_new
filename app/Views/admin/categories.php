<?php include __DIR__ . '/header.php'; ?>
        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Manage Categories</h1>
                    <p style="color: #718096; font-size: 14px;">Add, edit, and organize the "Explore Collections" grid</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px;">
                <!-- Category List -->
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 16px;">Existing Categories</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>IMAGE</th>
                                    <th>NAME &amp; SLUG</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $c): ?>
                                    <tr>
                                        <td>
                                            <?php if ($c['image']): ?>
                                                <img src="<?= htmlspecialchars($c['image']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: #e2e8f0; border-radius: 4px;"></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($c['name']) ?></div>
                                            <div style="font-size: 11px; color: var(--color-accent); font-weight: 700; margin-top: 2px;">/collections/<?= htmlspecialchars($c['slug']) ?></div>
                                        </td>
                                        <td>
                                            <?php $isActive = $c['is_active'] ?? 1; ?>
                                            <form action="<?= BASE_URL ?>/admin/category/toggle/<?= $c['id'] ?>" method="POST" style="display:inline;">
                                                <button type="submit" style="
                                                    border: none; cursor: pointer; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;
                                                    letter-spacing: 0.05em; transition: all 0.2s;
                                                    background: <?= $isActive ? '#22c55e' : '#ef4444' ?>;
                                                    color: #fff;
                                                " title="Click to <?= $isActive ? 'Deactivate' : 'Activate' ?>">
                                                    <?= $isActive ? '● ACTIVE' : '● INACTIVE' ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/admin/category/edit/<?= $c['id'] ?>" style="color: #181818; font-size: 12px; font-weight: 600; margin-right: 8px; text-decoration: none;">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($categories)): ?>
                                    <tr><td colspan="4" style="text-align: center; color: #718096; padding: 20px;">No categories found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Category Form -->
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 16px;">Add New Category</h3>
                    <div style="background: #fff; padding: 24px; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <form action="<?= BASE_URL ?>/admin/categories/add" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>CATEGORY NAME</label>
                                <input type="text" name="name" required placeholder="e.g. Black Abaya">
                            </div>

                            <div class="form-group">
                                <label>CATEGORY SLUG (Optional)</label>
                                <input type="text" name="slug" placeholder="e.g. black-abaya">
                                <span style="font-size: 11px; color: #718096;">Leave blank to auto-generate from name</span>
                            </div>

                            <div class="form-group">
                                <label>DESCRIPTION (Optional)</label>
                                <textarea name="description" rows="3" placeholder="Category description..."></textarea>
                            </div>

                            <div class="form-group">
                                <label style="font-weight: 700; color: #1e293b;">CATEGORY IMAGE (Explore Collections Thumbnail)</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                    <div>
                                        <label style="font-size: 11px; font-weight: normal; margin-bottom: 4px; display: block; color: #64748b;">Upload File (Overrides URL)</label>
                                        <input type="file" name="image_file" accept="image/*" onchange="handleCatImageFileChange(this)" style="padding: 6px; width: 100%; border: 1px dashed #cbd5e0; border-radius: 4px; background: #fff;">
                                    </div>
                                    <div>
                                        <label style="font-size: 11px; font-weight: normal; margin-bottom: 4px; display: block; color: #64748b;">Or Image URL</label>
                                        <input type="url" name="image" oninput="handleCatImageUrlChange(this)" placeholder="https://..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px;">
                                    </div>
                                </div>
                                <div id="catImagePreviewBox" style="display:none;"></div>
                                <div id="catUrlPreviewBox" style="display:none;"></div>
                            </div>

                            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Add Category</button>
                        </form>
                    </div>
                </div>
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
                        <div style="font-size:11px; font-weight:700; color:#16a34a; text-transform:uppercase;">SELECTED IMAGE PREVIEW</div>
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
