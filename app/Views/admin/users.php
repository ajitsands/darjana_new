<?php include __DIR__ . '/header.php'; ?>

        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Manage Admin Users</h1>
                    <p style="color: #718096; font-size: 14px;">Add new administrators to the portal and view existing accounts.</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 40px;">
                
                <!-- Add User Form -->
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 16px;">Add New Admin</h3>
                    <div style="background: #fff; padding: 24px; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <form action="<?= BASE_URL ?>/admin/users/add" method="POST">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" required placeholder="e.g. sarah_admin">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" required placeholder="Strong password">
                            </div>
                            <button type="submit" class="btn-primary" style="width: 100%;">Create Account</button>
                        </form>
                    </div>
                </div>

                <!-- Users List -->
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 16px;">Registered Administrators</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>USERNAME</th>
                                    <th>ACCOUNT CREATED</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td style="font-weight: 700; color: var(--color-accent);"><?= htmlspecialchars($user['username']) ?></td>
                                        <td style="font-size: 12px; color: #718096;"><?= date('M d, Y - h:i A', strtotime($user['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
