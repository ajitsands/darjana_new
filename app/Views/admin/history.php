<?php include __DIR__ . '/header.php'; ?>

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <style>
            .dataTables_wrapper .dataTables_filter input {
                border: 1px solid #ccc;
                border-radius: 4px;
                padding: 4px;
                margin-left: 8px;
            }
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #ccc;
                border-radius: 4px;
                padding: 4px;
            }
            table.dataTable tbody tr {
                background-color: transparent;
            }
            table.dataTable thead th, table.dataTable thead td {
                border-bottom: 1px solid var(--color-border);
            }
            table.dataTable.no-footer {
                border-bottom: 1px solid var(--color-border);
            }
        </style>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 26px;">Activity History</h1>
                <p style="color: #718096; font-size: 14px;">Audit log of all actions performed by administrators in the portal.</p>
            </div>
        </div>

        <!-- Date Filter Form -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <form method="GET" action="<?= BASE_URL ?>/admin/history" style="display: flex; gap: 15px; align-items: flex-end;">
                <div>
                    <label for="start_date" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div>
                    <label for="end_date" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div>
                    <button type="submit" style="padding: 9px 16px; background-color: var(--color-accent); color: #fff; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Filter</button>
                    <a href="<?= BASE_URL ?>/admin/history" style="display: inline-block; padding: 9px 16px; background-color: #e2e8f0; color: #4a5568; text-decoration: none; border-radius: 4px; font-weight: 600; margin-left: 10px;">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <?php if (empty($logs)): ?>
                <p style="color: #718096; text-align: center; padding: 20px;">No activity logged for the selected date range.</p>
            <?php else: ?>
                <table id="historyTable" class="display" style="width:100%">
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
                                <td style="font-size: 12px; color: #718096;" data-order="<?= strtotime($log['created_at']) ?>">
                                    <?= date('M d, Y - h:i A', strtotime($log['created_at'])) ?>
                                </td>
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

    </main>
    
    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($('#historyTable').length) {
                $('#historyTable').DataTable({
                    "order": [[ 0, "desc" ]], // Sort by DATE & TIME column descending by default
                    "pageLength": 25,
                    "responsive": true,
                    "language": {
                        "search": "Search Logs:"
                    }
                });
            }
        });
    </script>
</body>
</html>
