<?php include __DIR__ . '/header.php'; ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- DataTables RowGroup CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.0/css/rowGroup.dataTables.min.css">
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #ccc; border-radius: 4px; padding: 4px; margin-left: 8px; }
    .dataTables_wrapper .dataTables_length select { border: 1px solid #ccc; border-radius: 4px; padding: 4px; }
    table.dataTable tbody tr { background-color: transparent; }
    table.dataTable thead th, table.dataTable thead td { border-bottom: 1px solid var(--color-border); }
    table.dataTable.no-footer { border-bottom: 1px solid var(--color-border); }
    
    .group-btn {
        padding: 8px 16px;
        background: #f1f5f9;
        border: 1px solid #cbd5e0;
        cursor: pointer;
        border-radius: 4px;
        font-weight: 600;
        color: #4a5568;
    }
    .group-btn.active {
        background: var(--color-primary);
        color: #fff;
        border-color: var(--color-primary);
    }
    
    tr.dtrg-group td {
        background-color: #f8fafc !important;
        font-weight: bold;
        color: var(--color-primary);
    }
    
    .dt-buttons {
        margin-bottom: 15px;
        float: left;
    }
    .dataTables_filter {
        float: right;
        margin-bottom: 15px;
    }
    .dt-button.btn-primary {
        background: var(--color-primary) !important;
        color: #fff !important;
        border: none !important;
        padding: 8px 16px !important;
        border-radius: 4px !important;
        font-weight: 600 !important;
        cursor: pointer;
    }
</style>

<div class="admin-main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 26px;">Processing Job Orders</h1>
            <p style="color: #718096; font-size: 14px;">View all active job assignments</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/admin/tailoring-units" class="btn-secondary" style="background: #e2e8f0; color: #4a5568; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600;">&larr; Back to Tailoring Units</a>
        </div>
    </div>

    <?php if (isset($_SESSION['admin_success'])): ?>
        <div style="background-color: #def7ec; color: #03543f; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($_SESSION['admin_success']) ?>
        </div>
        <?php unset($_SESSION['admin_success']); ?>
    <?php endif; ?>

    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <button class="group-btn active" id="btnGroupOrder" onclick="setGrouping(0, this)">Group by Order Number</button>
        <button class="group-btn" id="btnGroupUnit" onclick="setGrouping(1, this)">Group by Tailoring Unit</button>
    </div>

    <!-- Filter Form -->
    <div style="background: #fff; padding: 15px 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <form method="GET" action="<?= BASE_URL ?>/admin/tailoring-units/processing-jobs" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">Status</label>
                <select name="status" style="padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px; min-width: 150px;">
                    <option value="">All Jobs</option>
                    <option value="Processing" <?= ($statusFilter === 'Processing') ? 'selected' : '' ?>>Processing Only</option>
                    <option value="Completed" <?= ($statusFilter === 'Completed') ? 'selected' : '' ?>>Completed Only</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">Start Date</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>" style="padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">End Date</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>" style="padding: 8px; border: 1px solid #cbd5e0; border-radius: 4px;">
            </div>
            <div>
                <button type="submit" class="btn-primary" style="background: var(--color-primary); color: #fff; padding: 9px 20px; border-radius: 4px; border: none; font-weight: 600; cursor: pointer;">Search</button>
                <a href="<?= BASE_URL ?>/admin/tailoring-units/processing-jobs" class="btn-secondary" style="background: #e2e8f0; color: #4a5568; padding: 9px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; margin-left: 5px; display: inline-block;">Clear</a>
            </div>
        </form>
    </div>

    <div class="table-responsive" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table id="jobsTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th style="display:none;">ORDER NUMBER</th>
                    <th style="display:none;">TAILORING UNIT</th>
                    <th>JOB PR CODE</th>
                    <th>PRODUCT</th>
                    <th>SPECS</th>
                    <th>QTY</th>
                    <th>ORDER DATE</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $job): ?>
                    <tr>
                        <td style="display:none;"><?= htmlspecialchars($job['order_number']) ?></td>
                        <td style="display:none;"><?= htmlspecialchars($job['unit_name']) ?> (<?= htmlspecialchars($job['unique_unit_code']) ?>)</td>
                        
                        <td style="font-weight: 600; color: var(--color-accent);"><?= htmlspecialchars($job['process_number'] ?? 'N/A') ?></td>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($job['product_name']) ?></div>
                            <div style="font-size: 11px; color: #718096;"><?= htmlspecialchars($job['product_code']) ?></div>
                        </td>
                        <td>
                            <div style="font-size: 12px;">Size: <?= htmlspecialchars($job['size']) ?> | Color: <?= htmlspecialchars($job['color']) ?> | Length: <?= htmlspecialchars($job['length']) ?></div>
                            <?php if(!empty($job['note'])): ?>
                                <div style="font-size: 11px; color: #e53e3e; margin-top: 2px;">Note: <?= htmlspecialchars($job['note']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: bold;"><?= htmlspecialchars($job['quantity']) ?></td>
                        <td><?= date('M d, Y H:i', strtotime($job['order_date'])) ?></td>
                        <td>
                            <?php if (($job['status'] ?? 'Processing') === 'Completed'): ?>
                                <span style="background-color: #def7ec; color: #03543f; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Completed</span>
                            <?php else: ?>
                                <span style="background-color: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Processing</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($job['status'] ?? 'Processing') !== 'Completed'): ?>
                                <a href="#" onclick="confirmComplete(event, '<?= BASE_URL ?>/admin/tailoring-units/complete-job/<?= $job['id'] ?>')" style="color: #046c4e; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    Mark Completed
                                </a>
                            <?php else: ?>
                                <span style="color: #a0aec0; font-size: 13px; font-weight: 600;">Done</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.4.0/js/dataTables.rowGroup.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let table;

$(document).ready(function() {
    table = $('#jobsTable').DataTable({
        "order": [[0, 'desc']], // Initially order by Order Number
        "pageLength": 25,
        "rowGroup": {
            dataSrc: 0 // Default group by Order Number (column index 0)
        },
        "dom": '<"top"Bf>rt<"bottom"lip><"clear">', // Add Buttons to the DOM
        "buttons": [
            {
                extend: 'print',
                text: 'Print Job Orders',
                className: 'btn-primary',
                exportOptions: {
                    columns: [2, 3, 4, 5, 6, 7] // Exclude hidden ID columns (0,1) and ACTION column (8)
                },
                title: 'Processing Job Orders'
            }
        ]
    });
});

function setGrouping(colIndex, btn) {
    // Update active button state
    document.querySelectorAll('.group-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Update DataTable RowGroup and Order
    table.order([colIndex, 'desc']).draw();
    table.rowGroup().dataSrc(colIndex);
    table.draw();
}

function confirmComplete(e, url) {
    e.preventDefault();
    Swal.fire({
        title: 'Mark as Completed?',
        text: "Are you sure this job has been completed by the tailoring unit?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#046c4e',
        cancelButtonColor: '#718096',
        confirmButtonText: 'Yes, Complete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>

</body>
</html>
