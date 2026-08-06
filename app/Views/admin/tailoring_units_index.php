<?php include __DIR__ . '/header.php'; ?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #ccc; border-radius: 4px; padding: 4px; margin-left: 8px; }
    .dataTables_wrapper .dataTables_length select { border: 1px solid #ccc; border-radius: 4px; padding: 4px; }
    table.dataTable tbody tr { background-color: transparent; }
    table.dataTable thead th, table.dataTable thead td { border-bottom: 1px solid var(--color-border); }
    table.dataTable.no-footer { border-bottom: 1px solid var(--color-border); }
</style>

<div class="admin-main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 26px;">Tailoring Units</h1>
            <p style="color: #718096; font-size: 14px;">Manage the tailoring units</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/tailoring-units/create" class="btn-primary">+ Add Tailoring Unit</a>
    </div>

    <?php if (isset($_SESSION['admin_success'])): ?>
        <div style="background-color: #def7ec; color: #03543f; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <?= htmlspecialchars($_SESSION['admin_success']) ?>
        </div>
        <?php unset($_SESSION['admin_success']); ?>
    <?php endif; ?>

    <div class="table-responsive" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <?php if (empty($units)): ?>
            <p style="color: #718096; text-align: center; padding: 20px;">No tailoring units found.</p>
        <?php else: ?>
            <table id="unitsTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>UNIT CODE</th>
                        <th>UNIT NAME</th>
                        <th>CONTACT PERSON</th>
                        <th>PHONE</th>
                        <th>EMAIL</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($units as $unit): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--color-accent);"><?= htmlspecialchars($unit['unique_unit_code']) ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($unit['unit_name']) ?></td>
                            <td><?= htmlspecialchars($unit['contact_person']) ?></td>
                            <td><?= htmlspecialchars($unit['contact_number']) ?></td>
                            <td><?= htmlspecialchars($unit['email_id']) ?></td>
                            <td>
                                <?php if ($unit['is_active']): ?>
                                    <span style="background-color: #def7ec; color: #03543f; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Active</span>
                                <?php else: ?>
                                    <span style="background-color: #fde8e8; color: #9b1c1c; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/tailoring-units/edit/<?= $unit['id'] ?>" style="color: #3182ce; text-decoration: none; font-weight: 600; margin-right: 15px;">Edit</a>
                                <a href="#" onclick="confirmDelete(event, '<?= BASE_URL ?>/admin/tailoring-units/delete/<?= $unit['id'] ?>')" style="color: #e53e3e; text-decoration: none; font-weight: 600;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        if ($('#unitsTable').length) {
            $('#unitsTable').DataTable({
                "pageLength": 25,
                "ordering": false,
                "language": {
                    "search": "Search Units:"
                }
            });
        }
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
