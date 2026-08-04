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
                                    <th>NAME & SLUG</th>
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
                                            <a href="<?= BASE_URL ?>/admin/category/edit/<?= $c['id'] ?>" style="color: #181818; font-size: 12px; font-weight: 600; margin-right: 8px; text-decoration: none;">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($categories)): ?>
                                    <tr><td colspan="3" style="text-align: center; color: #718096; padding: 20px;">No categories found.</td></tr>
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
                                <label>CATEGORY IMAGE (Explore Collections Thumbnail)</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                    <div>
                                        <label style="font-size: 11px; font-weight: normal; margin-bottom: 4px; display: block;">Upload File (Overrides URL)</label>
                                        <input type="file" name="image_file" accept="image/*" style="padding: 6px;">
                                    </div>
                                    <div>
                                        <label style="font-size: 11px; font-weight: normal; margin-bottom: 4px; display: block;">Or Image URL</label>
                                        <input type="url" name="image" placeholder="https://...">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
