<?php include __DIR__ . '/header.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; margin: 0 0 6px 0; color: #1a202c;">Manage Admin Users</h1>
            <p style="color: #718096; font-size: 14px; margin: 0;">Add new administrators, update accounts, reset credentials, and manage system access.</p>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['admin_success'])): ?>
        <div style="background: #f0fff4; border-left: 4px solid #38a169; color: #22543d; padding: 14px 20px; border-radius: 4px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#38a169" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span style="font-size: 14px; font-weight: 500;"><?= htmlspecialchars($_SESSION['admin_success']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 18px; color: #22543d; cursor: pointer;">&times;</button>
        </div>
        <?php unset($_SESSION['admin_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['admin_error'])): ?>
        <div style="background: #fff5f5; border-left: 4px solid #e53e3e; color: #9b2c2c; padding: 14px 20px; border-radius: 4px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e53e3e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                <span style="font-size: 14px; font-weight: 500;"><?= htmlspecialchars($_SESSION['admin_error']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 18px; color: #9b2c2c; cursor: pointer;">&times;</button>
        </div>
        <?php unset($_SESSION['admin_error']); ?>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 360px 1fr; gap: 30px; align-items: start;">
        
        <!-- Add User Form -->
        <div style="background: #fff; padding: 26px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #edf2f7;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #edf2f7;">
                <div style="width: 32px; height: 32px; border-radius: 6px; background: #f7fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a5568" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: #2d3748;">Add New Admin</h3>
                    <p style="font-size: 12px; color: #a0aec0; margin: 2px 0 0 0;">Create a new administrator login</p>
                </div>
            </div>

            <form action="<?= BASE_URL ?>/admin/users/add" method="POST" autocomplete="off">
                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600; color: #4a5568; margin-bottom: 6px; display: block;">Username</label>
                    <input type="text" name="username" required minlength="3" placeholder="e.g. sarah_admin" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                    <span style="font-size: 11px; color: #a0aec0; margin-top: 4px; display: block;">Min 3 characters, unique name</span>
                </div>
                <div class="form-group" style="position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="font-size: 12px; font-weight: 600; color: #4a5568; margin: 0;">Password</label>
                        <button type="button" onclick="generatePassword('create_password_input')" style="background: none; border: none; color: #3182ce; font-size: 11px; font-weight: 600; cursor: pointer; padding: 0;">🎲 Generate</button>
                    </div>
                    <div style="position: relative;">
                        <input type="password" id="create_password_input" name="password" required minlength="6" placeholder="Min 6 characters" style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                        <button type="button" onclick="togglePasswordVisibility('create_password_input', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #a0aec0; cursor: pointer; padding: 4px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <span style="font-size: 11px; color: #a0aec0; margin-top: 4px; display: block;">Min 6 characters</span>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; border-radius: 6px; font-weight: 600; margin-top: 10px; background: #181818; color: #fff; border: none; cursor: pointer; transition: background 0.2s ease;">Create Account</button>
            </form>
        </div>

        <!-- Users List Table -->
        <div style="background: #fff; padding: 26px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border: 1px solid #edf2f7;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #edf2f7;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: #2d3748;">Registered Administrators</h3>
                    <p style="font-size: 12px; color: #a0aec0; margin: 2px 0 0 0;">Total active accounts: <?= count($users) ?></p>
                </div>
            </div>

            <div class="table-responsive" style="box-shadow: none; padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                            <th style="padding: 12px 16px; font-size: 11px; font-weight: 700; color: #718096; letter-spacing: 0.08em; text-align: left;">ID</th>
                            <th style="padding: 12px 16px; font-size: 11px; font-weight: 700; color: #718096; letter-spacing: 0.08em; text-align: left;">ADMINISTRATOR</th>
                            <th style="padding: 12px 16px; font-size: 11px; font-weight: 700; color: #718096; letter-spacing: 0.08em; text-align: left;">ACCOUNT CREATED</th>
                            <th style="padding: 12px 16px; font-size: 11px; font-weight: 700; color: #718096; letter-spacing: 0.08em; text-align: right;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $currentUserId = $_SESSION['user_id'] ?? 0;
                            $totalAdmins = count($users);
                        ?>
                        <?php foreach ($users as $user): ?>
                            <?php $isSelf = ($currentUserId == $user['id']); ?>
                            <tr style="border-bottom: 1px solid #edf2f7; transition: background 0.15s ease;">
                                <td style="padding: 14px 16px; font-size: 13px; color: #718096; font-weight: 600;">
                                    #<?= $user['id'] ?>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #c5a880 0%, #a8895e 100%); color: #111; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px;">
                                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="display: flex; align-items: center; gap: 6px;">
                                                <span style="font-weight: 700; font-size: 14px; color: #2d3748;"><?= htmlspecialchars($user['username']) ?></span>
                                                <?php if ($isSelf): ?>
                                                    <span style="font-size: 10px; font-weight: 700; background: #ebf8ff; color: #2b6cb0; padding: 2px 6px; border-radius: 10px; border: 1px solid #bee3f8;">YOU</span>
                                                <?php endif; ?>
                                            </div>
                                            <span style="font-size: 11px; color: #a0aec0;">System Administrator</span>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 14px 16px; font-size: 13px; color: #718096;">
                                    <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                    <div style="font-size: 11px; color: #a0aec0;"><?= date('h:i A', strtotime($user['created_at'])) ?></div>
                                </td>
                                <td style="padding: 14px 16px; text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        
                                        <!-- Edit Username / Account -->
                                        <button type="button" 
                                                onclick="openEditModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['username'])) ?>')"
                                                style="display: inline-flex; align-items: center; gap: 5px; background: #edf2f7; border: 1px solid #cbd5e0; color: #2d3748; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Edit
                                        </button>

                                        <!-- Reset Password -->
                                        <button type="button" 
                                                onclick="openResetModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['username'])) ?>')"
                                                style="display: inline-flex; align-items: center; gap: 5px; background: #ebf8ff; border: 1px solid #bee3f8; color: #2b6cb0; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                            Reset Password
                                        </button>

                                        <!-- Delete Account -->
                                        <?php if ($isSelf): ?>
                                            <button type="button" disabled title="You cannot delete your own active administrator account"
                                                    style="display: inline-flex; align-items: center; gap: 5px; background: #edf2f7; border: 1px solid #e2e8f0; color: #a0aec0; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                Delete
                                            </button>
                                        <?php elseif ($totalAdmins <= 1): ?>
                                            <button type="button" disabled title="Cannot delete the only remaining administrator"
                                                    style="display: inline-flex; align-items: center; gap: 5px; background: #edf2f7; border: 1px solid #e2e8f0; color: #a0aec0; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                Delete
                                            </button>
                                        <?php else: ?>
                                            <button type="button" 
                                                    onclick="confirmDelete(event, '<?= BASE_URL ?>/admin/users/delete/<?= $user['id'] ?>', 'Are you sure you want to delete administrator <?= htmlspecialchars(addslashes($user['username'])) ?>?')"
                                                    style="display: inline-flex; align-items: center; gap: 5px; background: #fff5f5; border: 1px solid #feb2b2; color: #e53e3e; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                Delete
                                            </button>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #fff; width: 100%; max-width: 460px; border-radius: 10px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); overflow: hidden; animation: modalSlideUp 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
            <div style="background: #181818; color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c5a880" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #fff;">Edit Administrator</h3>
                </div>
                <button type="button" onclick="closeEditModal()" style="background: none; border: none; color: #a0aec0; font-size: 22px; cursor: pointer; line-height: 1;">&times;</button>
            </div>
            
            <form id="editUserForm" method="POST" action="" style="padding: 24px;">
                <div class="form-group" style="margin-bottom: 18px;">
                    <label style="font-size: 12px; font-weight: 600; color: #4a5568; margin-bottom: 6px; display: block;">Username</label>
                    <input type="text" id="edit_username_input" name="username" required minlength="3" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="font-size: 12px; font-weight: 600; color: #4a5568; margin: 0;">New Password (Optional)</label>
                        <button type="button" onclick="generatePassword('edit_password_input')" style="background: none; border: none; color: #3182ce; font-size: 11px; font-weight: 600; cursor: pointer; padding: 0;">🎲 Generate</button>
                    </div>
                    <div style="position: relative;">
                        <input type="password" id="edit_password_input" name="password" minlength="6" placeholder="Leave blank to keep unchanged" style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                        <button type="button" onclick="togglePasswordVisibility('edit_password_input', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #a0aec0; cursor: pointer; padding: 4px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <span style="font-size: 11px; color: #a0aec0; margin-top: 4px; display: block;">Only fill this in if you wish to change the password</span>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="closeEditModal()" style="padding: 10px 18px; border-radius: 6px; background: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; font-weight: 600; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-primary" style="padding: 10px 22px; border-radius: 6px; font-weight: 600; background: #181818; color: #fff; border: none; cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #fff; width: 100%; max-width: 440px; border-radius: 10px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); overflow: hidden; animation: modalSlideUp 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
            <div style="background: #181818; color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#63b3ed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #fff;">Reset Password</h3>
                </div>
                <button type="button" onclick="closeResetModal()" style="background: none; border: none; color: #a0aec0; font-size: 22px; cursor: pointer; line-height: 1;">&times;</button>
            </div>
            
            <form id="resetPasswordForm" method="POST" action="" style="padding: 24px;">
                <p style="font-size: 13px; color: #4a5568; margin: 0 0 18px 0;">
                    Resetting password for: <strong id="reset_modal_username" style="color: #2b6cb0;"></strong>
                </p>

                <div class="form-group" style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="font-size: 12px; font-weight: 600; color: #4a5568; margin: 0;">New Password</label>
                        <button type="button" onclick="generatePassword('reset_password_input')" style="background: none; border: none; color: #3182ce; font-size: 11px; font-weight: 600; cursor: pointer; padding: 0;">🎲 Generate Strong</button>
                    </div>
                    <div style="position: relative;">
                        <input type="password" id="reset_password_input" name="new_password" required minlength="6" placeholder="Enter new password (min 6 chars)" style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                        <button type="button" onclick="togglePasswordVisibility('reset_password_input', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #a0aec0; cursor: pointer; padding: 4px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <span style="font-size: 11px; color: #a0aec0; margin-top: 4px; display: block;">Minimum 6 characters required</span>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="closeResetModal()" style="padding: 10px 18px; border-radius: 6px; background: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; font-weight: 600; cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-primary" style="padding: 10px 22px; border-radius: 6px; font-weight: 600; background: #2b6cb0; color: #fff; border: none; cursor: pointer;">Confirm Reset</button>
                </div>
            </form>
        </div>
    </div>

    <style>
    @keyframes modalSlideUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>

    <script>
    const baseUrl = '<?= BASE_URL ?>';

    function openEditModal(id, username) {
        const modal = document.getElementById('editUserModal');
        const form = document.getElementById('editUserForm');
        const usernameInput = document.getElementById('edit_username_input');
        const passwordInput = document.getElementById('edit_password_input');

        form.action = baseUrl + '/admin/users/update/' + id;
        usernameInput.value = username;
        passwordInput.value = '';
        modal.style.display = 'flex';
        usernameInput.focus();
    }

    function closeEditModal() {
        document.getElementById('editUserModal').style.display = 'none';
    }

    function openResetModal(id, username) {
        const modal = document.getElementById('resetPasswordModal');
        const form = document.getElementById('resetPasswordForm');
        const usernameLabel = document.getElementById('reset_modal_username');
        const passwordInput = document.getElementById('reset_password_input');

        form.action = baseUrl + '/admin/users/reset-password/' + id;
        usernameLabel.textContent = username;
        passwordInput.value = '';
        modal.style.display = 'flex';
        passwordInput.focus();
    }

    function closeResetModal() {
        document.getElementById('resetPasswordModal').style.display = 'none';
    }

    function generatePassword(inputId) {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#$&*';
        let pass = '';
        for (let i = 0; i < 10; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const input = document.getElementById(inputId);
        input.value = pass;
        input.type = 'text'; // Show when generated so admin can copy/note it down
        
        // Find toggle button if adjacent to show eye icon in open state
        const btn = input.nextElementSibling;
        if (btn) {
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
        }
    }

    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
        } else {
            input.type = 'password';
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        }
    }

    // Close modals on clicking outside or ESC key
    window.addEventListener('click', function(e) {
        const editModal = document.getElementById('editUserModal');
        const resetModal = document.getElementById('resetPasswordModal');
        if (e.target === editModal) closeEditModal();
        if (e.target === resetModal) closeResetModal();
    });

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeResetModal();
        }
    });
    </script>

</main>
</body>
</html>
