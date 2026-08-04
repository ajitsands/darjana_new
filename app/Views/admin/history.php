<?php include __DIR__ . '/header.php'; ?>

        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Activity History</h1>
                    <p style="color: #718096; font-size: 14px;">Audit log of all actions performed by administrators in the portal.</p>
                </div>
            </div>

            <div class="table-responsive">
                <?php if (empty($logs)): ?>
                    <p style="color: #718096; text-align: center; padding: 20px;">No activity logged yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>DATE & TIME</th>
                                <th>ADMIN USER</th>
                                <th>ACTION TYPE</th>
                                <th>DESCRIPTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td style="font-size: 12px; color: #718096;"><?= date('M d, Y - h:i A', strtotime($log['created_at'])) ?></td>
                                    <td style="font-weight: 700; color: #2b6cb0;">
                                        <?= htmlspecialchars($log['username'] ?? 'System') ?>
                                    </td>
                                    <td>
                                        <span style="font-size: 11px; font-weight: 700; background: #e2e8f0; padding: 2px 6px; border-radius: 3px;">
                                            <?= htmlspecialchars($log['action_type']) ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 13px; color: #4a5568;">
                                        <?= htmlspecialchars($log['description']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>
    </main>
</body>
</html>
