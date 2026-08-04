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
                        <label>CATEGORY IMAGE</label>
                        <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 15px;">
                            <?php if ($category['image']): ?>
                                <img src="<?= htmlspecialchars($category['image']) ?>" style="width: 120px; height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <?php else: ?>
                                <div style="width: 120px; height: 120px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #a0aec0; font-size: 12px;">No Image</div>
                            <?php endif; ?>
                            
                            <div style="flex: 1;">
                                <div style="margin-bottom: 10px;">
                                    <label style="font-size: 12px; font-weight: normal; margin-bottom: 4px; display: block;">Upload New File (Overrides existing image)</label>
                                    <input type="file" name="image_file" accept="image/*" style="padding: 8px;">
                                </div>
                                <div>
                                    <label style="font-size: 12px; font-weight: normal; margin-bottom: 4px; display: block;">Or update Image URL</label>
                                    <input type="url" name="image" value="<?= htmlspecialchars($category['image']) ?>" placeholder="https://...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 14px 30px; font-size: 15px;">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
