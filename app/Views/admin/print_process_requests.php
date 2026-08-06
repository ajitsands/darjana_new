<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Requests - Order #<?= htmlspecialchars($order['order_number']) ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 20px; background: #f3f4f6; color: #1a202c; }
        .page { background: #fff; width: 210mm; min-height: 297mm; padding: 20mm; margin: 0 auto 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); box-sizing: border-box; position: relative; }
        h1, h2, h3 { margin-top: 0; color: #1a202c; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
        .header-left { max-width: 60%; }
        .header-right { text-align: right; }
        
        .brand { font-size: 24px; font-weight: 700; color: #c5a059; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 2px; }
        .pr-number { font-size: 20px; font-weight: 700; margin-bottom: 5px; }
        .meta { color: #4a5568; font-size: 14px; margin-bottom: 3px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; border: 1px solid #cbd5e0; text-align: left; }
        th { background-color: #f7fafc; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: #4a5568; }
        td { font-size: 14px; vertical-align: top; }
        
        .remarks { font-style: italic; color: #718096; font-size: 13px; }
        .qty { font-weight: 700; text-align: center; }

        .footer { position: absolute; bottom: 20mm; left: 20mm; right: 20mm; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 12px; color: #a0aec0; text-align: center; }

        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; margin: 0; padding: 15mm; min-height: auto; page-break-after: always; }
            .page:last-child { page-break-after: avoid; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3182ce; color: #fff; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 16px;">
            🖨️ Print Process Requests
        </button>
    </div>

    <?php foreach ($groupedRequests as $request): ?>
    <div class="page">
        <div class="header">
            <div class="header-left">
                <div class="brand"><img src="<?= BASE_URL ?>/assets/images/web_logo_menu.png" alt="Dar Jana Fashion" style="max-height: 40px; margin-bottom: 10px;"></div>
                <div style="font-size: 16px; font-weight: 600; color: #4a5568; margin-bottom: 15px;">Tailoring Process Request</div>
                
                <div class="meta"><strong>Tailoring Unit:</strong> <?= htmlspecialchars($request['unit_name']) ?></div>
                <div class="meta"><strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?></div>
                <div class="meta"><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
            </div>
            <div class="header-right">
                <div class="pr-number"><?= htmlspecialchars($request['process_number']) ?></div>
                <div class="meta"><strong>Date:</strong> <?= date('d M Y, h:i A') ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Details</th>
                    <th>Specifications</th>
                    <th>Remarks / Notes</th>
                    <th style="text-align: center;">QTY</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($request['items'] as $item): ?>
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
                    <td class="remarks">
                        <?= !empty($item['note']) ? nl2br(htmlspecialchars($item['note'])) : 'None' ?>
                    </td>
                    <td class="qty">
                        <?= $item['assigned_quantity'] ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 50px;">
            <div style="display: flex; justify-content: space-between;">
                <div style="border-top: 1px solid #cbd5e0; width: 250px; text-align: center; padding-top: 10px; font-size: 14px; color: #4a5568;">
                    Issued By (Admin)
                </div>
                <div style="border-top: 1px solid #cbd5e0; width: 250px; text-align: center; padding-top: 10px; font-size: 14px; color: #4a5568;">
                    Received By (Tailoring Unit)
                </div>
            </div>
        </div>

        <div class="footer">
            Generated by Dar Jana Fashion Order Management System &bull; <?= htmlspecialchars($request['process_number']) ?>
        </div>
    </div>
    <?php endforeach; ?>

</body>
</html>
