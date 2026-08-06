<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Summary - Order #<?= htmlspecialchars($order['order_number']) ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 20px; background: #f3f4f6; color: #1a202c; }
        .page { background: #fff; width: 210mm; min-height: 297mm; padding: 20mm; margin: 0 auto 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); box-sizing: border-box; position: relative; }
        h1, h2, h3 { margin-top: 0; color: #1a202c; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
        .header-left { max-width: 60%; }
        .header-right { text-align: right; }
        
        .brand { margin-bottom: 5px; }
        .pr-number { font-size: 20px; font-weight: 700; margin-bottom: 5px; color: #c5a059; }
        .meta { color: #4a5568; font-size: 14px; margin-bottom: 3px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; border: 1px solid #cbd5e0; text-align: left; }
        th { background-color: #f7fafc; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: #4a5568; }
        td { font-size: 14px; vertical-align: top; }
        
        .remarks { font-style: italic; color: #718096; font-size: 13px; }
        .qty { font-weight: 700; text-align: center; }

        .assignment-badge { background: #edf2f7; padding: 6px 10px; border-radius: 4px; margin-bottom: 6px; font-size: 12px; border: 1px solid #e2e8f0; }
        .assignment-badge strong { color: #2b6cb0; }
        .assignment-pr { color: #718096; font-size: 11px; display: block; margin-top: 2px; }

        .footer { position: absolute; bottom: 20mm; left: 20mm; right: 20mm; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 12px; color: #a0aec0; text-align: center; }

        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; margin: 0; padding: 15mm; min-height: auto; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4a5568; color: #fff; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 16px;">
            🖨️ Print Assignment Summary
        </button>
    </div>

    <div class="page">
        <div class="header">
            <div class="header-left">
                <div class="brand"><img src="<?= BASE_URL ?>/assets/images/web_logo_menu.png" alt="Dar Jana Fashion" style="max-height: 40px; margin-bottom: 10px;"></div>
                <div style="font-size: 18px; font-weight: 700; color: #1a202c; margin-bottom: 5px;">Order Assignment Summary</div>
                
                <div class="meta"><strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?></div>
                <div class="meta"><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
            </div>
            <div class="header-right">
                <div class="meta"><strong>Date:</strong> <?= date('d M Y, h:i A') ?></div>
                <div class="meta"><strong>Total Line Items:</strong> <?= count($items) ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Details</th>
                    <th>Specifications</th>
                    <th style="text-align: center;">Total Qty</th>
                    <th>Tailoring Assignments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <div style="color: #c5a059; font-weight: 700; font-size: 12px; margin-bottom: 4px;"><?= htmlspecialchars($item['product_code']) ?></div>
                        <div style="font-weight: 600;"><?= htmlspecialchars($item['product_name']) ?></div>
                    </td>
                    <td>
                        <div><strong>Size:</strong> <?= htmlspecialchars($item['size'] ?: 'N/A') ?></div>
                        <div><strong>Length:</strong> <?= htmlspecialchars($item['length'] ?: 'N/A') ?>"</div>
                        <div><strong>Color:</strong> <?= htmlspecialchars($item['color'] ?: 'N/A') ?></div>
                    </td>
                    <td class="qty">
                        <?= $item['quantity'] ?>
                    </td>
                    <td>
                        <?php 
                        $itemAssignments = array_filter($assignments, function($a) use ($item) {
                            return $a['order_item_id'] == $item['id'];
                        });
                        $assignedQty = 0;
                        ?>
                        
                        <?php if (empty($itemAssignments)): ?>
                            <span style="color: #e53e3e; font-size: 12px; font-style: italic;">Not assigned</span>
                        <?php else: ?>
                            <?php foreach ($itemAssignments as $assignment): ?>
                                <?php $assignedQty += $assignment['quantity']; ?>
                                <div class="assignment-badge">
                                    <strong><?= htmlspecialchars($assignment['unit_name']) ?></strong> (Qty: <?= $assignment['quantity'] ?>)
                                    <span class="assignment-pr">Job Code: <?= htmlspecialchars($assignment['process_number']) ?></span>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if ($assignedQty < $item['quantity']): ?>
                                <div style="color: #dd6b20; font-size: 11px; margin-top: 4px; font-weight: 600;">⚠️ <?= ($item['quantity'] - $assignedQty) ?> item(s) unassigned</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer">
            Generated by Dar Jana Fashion Order Management System
        </div>
    </div>

</body>
</html>
