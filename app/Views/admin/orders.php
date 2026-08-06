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

        <div class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 26px;">Customer Orders</h1>
                    <p style="color: #718096; font-size: 14px;">Manage all incoming customer orders for Dar Jana Fashion</p>
                </div>
            </div>

            <!-- Date Filter Form -->
            <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <form method="GET" action="<?= BASE_URL ?>/admin/orders" style="display: flex; gap: 15px; align-items: flex-end;">
                    <div>
                        <label for="start_date" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    <div>
                        <label for="end_date" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    <div>
                        <label for="order_status" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">Order Status</label>
                        <select id="order_status" name="order_status" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #fff;">
                            <option value="All" <?= ($orderStatusFilter ?? 'All') === 'All' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="New" <?= ($orderStatusFilter ?? '') === 'New' ? 'selected' : '' ?>>New</option>
                            <option value="Processing" <?= ($orderStatusFilter ?? '') === 'Processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="Shipped" <?= ($orderStatusFilter ?? '') === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="Delivered" <?= ($orderStatusFilter ?? '') === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="Canceled" <?= ($orderStatusFilter ?? '') === 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_status" style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px; color: #4a5568;">Payment</label>
                        <select id="payment_status" name="payment_status" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #fff;">
                            <option value="All" <?= ($paymentStatusFilter ?? 'All') === 'All' ? 'selected' : '' ?>>All Payments</option>
                            <option value="Pending" <?= ($paymentStatusFilter ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Paid" <?= ($paymentStatusFilter ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="Failed" <?= ($paymentStatusFilter ?? '') === 'Failed' ? 'selected' : '' ?>>Failed</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" style="padding: 9px 16px; background-color: var(--color-accent); color: #fff; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Filter</button>
                        <a href="<?= BASE_URL ?>/admin/orders" style="display: inline-block; padding: 9px 16px; background-color: #e2e8f0; color: #4a5568; text-decoration: none; border-radius: 4px; font-weight: 600; margin-left: 10px;">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Recent Orders Section -->
            <div style="margin-bottom: 50px;">
                <div class="table-responsive" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <?php if (empty($orders)): ?>
                        <p style="color: #718096; text-align: center; padding: 20px;">No customer orders found.</p>
                    <?php else: ?>
                        <table id="ordersTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ORDER #</th>
                                    <th>CUSTOMER</th>
                                    <th>PHONE</th>
                                    <th>ADDRESS</th>
                                    <th>TOTAL</th>
                                    <th>STATUS</th>
                                    <th>DATE</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $ord): ?>
                                    <?php
                                        $payStatus = $ord['payment_status'] ?? 'Pending';
                                        if ($payStatus === 'Paid') {
                                            $payBg = '#def7ec';
                                            $payColor = '#03543f';
                                        } elseif ($payStatus === 'Failed') {
                                            $payBg = '#fde8e8';
                                            $payColor = '#9b1c1c';
                                        } else {
                                            $payBg = '#fef3c7';
                                            $payColor = '#92400e';
                                        }
                                        $apiResponse = !empty($ord['api_response']) ? htmlspecialchars($ord['api_response'], ENT_QUOTES, 'UTF-8') : '';
                                    ?>
                                    <tr>
                                        <td style="font-weight: 700; color: var(--color-accent);"><?= htmlspecialchars($ord['order_number']) ?></td>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                            <div style="font-size: 12px; color: #718096;"><?= htmlspecialchars($ord['email']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($ord['phone']) ?></td>
                                        <td><?= htmlspecialchars($ord['address']) ?>, <?= htmlspecialchars($ord['city']) ?></td>
                                        <td style="font-weight: 700;"><?= number_format($ord['total_amount'], 2) ?> BHD</td>
                                        <td>
                                            <div style="margin-bottom: 4px;">
                                                <span <?= $apiResponse ? 'onclick="showPaymentDetails(this)" data-response="' . $apiResponse . '"' : '' ?> style="background: <?= $payBg ?>; color: <?= $payColor ?>; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; cursor: <?= $apiResponse ? 'pointer' : 'default' ?>;">PAY: <?= htmlspecialchars($payStatus) ?></span>
                                            </div>
                                            <div>
                                                <span style="background: #fef3c7; color: #92400e; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase;">ORD: <?= htmlspecialchars($ord['status']) ?></span>
                                            </div>
                                        </td>
                                        <td style="font-size: 12px; color: #718096;" data-order="<?= strtotime($ord['created_at']) ?>"><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/admin/order/<?= $ord['id'] ?>" style="color: var(--color-accent); font-size: 12px; font-weight: 600; margin-right: 12px;">View Details</a>
                                            <a href="<?= BASE_URL ?>/admin/order/delete/<?= $ord['id'] ?>" onclick="confirmDelete(event, this.href, 'Delete this order?')" style="color: #e53e3e; font-size: 12px; font-weight: 600;">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- jQuery and DataTables JS -->
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                if ($('#ordersTable').length) {
                    $('#ordersTable').DataTable({
                        "order": [[ 6, "desc" ]], // Sort by DATE column descending by default
                        "pageLength": 25,
                        "responsive": true,
                        "language": {
                            "search": "Search Orders:"
                        }
                    });
                }
            });
        </script>

<!-- Payment Details Modal -->
<div id="paymentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:30px; border-radius:8px; width:400px; max-width:90%; position:relative; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:20px;">Payment Details</h3>
        
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <strong style="color:#4a5568;">Type:</strong> <span id="pmt_type" style="color:#2d3748;"></span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <strong style="color:#4a5568;">Card Number:</strong> <span id="pmt_card" style="color:#2d3748;"></span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <strong style="color:#4a5568;">Card Holder:</strong> <span id="pmt_name" style="color:#2d3748;"></span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <strong style="color:#4a5568;">Date/Time:</strong> <span id="pmt_date" style="color:#2d3748;"></span>
        </div>
        <div style="margin-top:15px; padding:10px; background:#f7fafc; border-radius:4px; font-size:13px; color:#4a5568;">
            <strong>Result:</strong> <span id="pmt_result"></span>
        </div>
        
        <button onclick="closePaymentModal()" style="margin-top:25px; width:100%; padding:10px; background:#e2e8f0; border:none; border-radius:4px; font-weight:600; cursor:pointer;">Close</button>
    </div>
</div>

<script>
function showPaymentDetails(el) {
    try {
        let rawData = el.getAttribute('data-response');
        if (!rawData) return;
        const data = JSON.parse(rawData);
        
        $('#pmt_type').text(data.paymentBrand || 'Unknown');
        
        if (data.card) {
            $('#pmt_card').text((data.card.bin ? data.card.bin : '') + '******' + (data.card.last4Digits ? data.card.last4Digits : ''));
            $('#pmt_name').text(data.card.holder || 'N/A');
        } else {
            $('#pmt_card').text('N/A');
            $('#pmt_name').text('N/A');
        }
        
        $('#pmt_date').text(data.timestamp || 'N/A');
        $('#pmt_result').text((data.result && data.result.description) ? data.result.description : 'N/A');
        
        $('#paymentModal').css('display', 'flex');
    } catch (e) {
        console.error(e);
        alert('Could not parse payment details.');
    }
}

function closePaymentModal() {
    $('#paymentModal').hide();
}

// Close on outside click
$('#paymentModal').click(function(e) {
    if (e.target.id === 'paymentModal') closePaymentModal();
});
</script>

</body>
</html>
