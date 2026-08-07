<?php include __DIR__ . '/header.php'; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper { font-size: 13.5px; margin-top: 10px; }
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #cbd5e0; padding: 6px 12px; border-radius: 6px; margin-left: 8px; font-size: 13px; }
    .dataTables_wrapper .dataTables_length select { border: 1px solid #cbd5e0; padding: 4px 8px; border-radius: 6px; font-size: 13px; }
    table.dataTable thead th { border-bottom: 2px solid #e2e8f0 !important; font-size: 11px; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 14px; background: #f8fafc; }
    table.dataTable tbody td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    /* Modal Styles */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.55); z-index: 99999; backdrop-filter: blur(3px); align-items: center; justify-content: center; }
    .modal-card { background: #ffffff; border-radius: 12px; width: 90%; max-width: 850px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.2); display: flex; flex-direction: column; }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #181818; color: #ffffff; border-radius: 12px 12px 0 0; }
    .modal-body { padding: 24px; flex: 1; }
    .modal-footer { padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end; gap: 12px; border-radius: 0 0 12px 12px; }
</style>

<div class="admin-main" style="padding: 28px; background: #f8fafc; min-height: 100vh;">
    <!-- Top Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="<?= BASE_URL ?>/admin" style="color: #64748b; font-size: 13px; text-decoration: none; font-weight: 600;">← Dashboard</a>
                <span style="color: #cbd5e0;">/</span>
                <span style="color: #c5a059; font-size: 13px; font-weight: 700;">Subscribers &amp; Promo</span>
            </div>
            <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin-top: 6px;">Newsletter Subscribers &amp; Promotional Campaigns</h1>
            <p style="color: #64748b; font-size: 13px; margin-top: 2px;">View subscriber list and send promotional product emails with custom messages</p>
        </div>
        <button type="button" onclick="openPromoModal()" style="background: #181818; color: #ffffff; border: none; padding: 12px 22px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            ✉️ Create Promotional Email Campaign
        </button>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-left: 4px solid #c5a059;">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">TOTAL SUBSCRIBERS</div>
            <div style="font-size: 28px; font-weight: 800; color: #181818; margin-top: 6px;"><?= count($subscribers) ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Registered newsletter emails</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-left: 4px solid #22c55e;">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">ACTIVE AUDIENCE</div>
            <div style="font-size: 28px; font-weight: 800; color: #22c55e; margin-top: 6px;"><?= count(array_filter($subscribers, fn($s) => ($s['status'] ?? 'active') === 'active')) ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Ready to receive promos</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border-left: 4px solid #3b82f6;">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">PRODUCTS AVAILABLE FOR PROMO</div>
            <div style="font-size: 28px; font-weight: 800; color: #3b82f6; margin-top: 6px;"><?= count($products) ?></div>
            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Active catalog items</div>
        </div>
    </div>

    <!-- Subscriber Table Section -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">📋 Subscriber List</h3>
                <p style="font-size: 12.5px; color: #64748b; margin-top: 2px;">Select subscribers to send direct promotional offers or manage your mailing audience</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="sendSelectedPromo()" style="background: #c5a059; color: #ffffff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 12.5px; cursor: pointer;">
                    ✉️ Send Promo to Selected (<span id="selectedCountBadge">0</span>)
                </button>
            </div>
        </div>

        <?php if (!empty($subscribers)): ?>
            <div class="table-responsive">
                <table id="subscribersTable" class="display" style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                    <thead>
                        <tr>
                            <th style="width: 30px; text-align: center;"><input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)"></th>
                            <th>Subscriber Email</th>
                            <th>Subscribed Date</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscribers as $s): ?>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" class="sub-checkbox" value="<?= htmlspecialchars($s['email']) ?>" onchange="updateSelectedBadge()">
                                </td>
                                <td style="font-weight: 600; color: #1e293b;">
                                    <?= htmlspecialchars($s['email']) ?>
                                </td>
                                <td style="color: #64748b; font-size: 12.5px;" data-order="<?= !empty($s['created_at']) ? strtotime($s['created_at']) : 0 ?>">
                                    <?= !empty($s['created_at']) ? date('d M Y, h:i A', strtotime($s['created_at'])) : 'Recent' ?>
                                </td>
                                <td>
                                    <span style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 12px;">
                                        Active
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                        <button type="button" onclick="sendSinglePromo('<?= htmlspecialchars($s['email']) ?>')" style="background: #f8fafc; border: 1px solid #cbd5e0; color: #181818; padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                            ✉️ Send Promo
                                        </button>
                                        <form method="POST" action="<?= BASE_URL ?>/admin/subscribers/delete/<?= $s['id'] ?>" onsubmit="return confirmDeleteForm(event, this, 'Are you sure you want to delete subscriber <?= htmlspecialchars($s['email']) ?>?');" style="margin:0;">
                                            <button type="submit" style="background: #fef2f2; border: 1px solid #fecaca; color: #ef4444; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #94a3b8; font-style: italic;">No newsletter subscribers recorded yet.</div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== PROMOTIONAL EMAIL CAMPAIGN MODAL ===== -->
<div class="modal-overlay" id="promoCampaignModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="font-size: 17px; font-weight: 700; margin: 0;">✉️ Create Product Promotional Campaign</h3>
            <button type="button" onclick="closePromoModal()" style="background: none; border: none; color: #ffffff; font-size: 22px; cursor: pointer;">✕</button>
        </div>
        <form id="promoCampaignForm" onsubmit="submitPromoCampaign(event)">
            <div class="modal-body">
                
                <!-- Target Audience Selection -->
                <div style="margin-bottom: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                    <label style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 10px;">Target Audience</label>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer;">
                            <input type="radio" name="target_audience" value="all" checked id="targetAudienceAll">
                            All Active Subscribers (<?= count($subscribers) ?> recipients)
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer;">
                            <input type="radio" name="target_audience" value="selected" id="targetAudienceSelected">
                            Selected Subscribers Only (<span id="modalSelectedCount">0</span> selected)
                        </label>
                    </div>
                </div>

                <!-- Product Selection -->
                <div class="form-group">
                    <label style="font-size: 12.5px; font-weight: 700; color: #1e293b;">Select Product to Promote *</label>
                    <select name="product_id" id="promoProductId" required onchange="onProductSelectChange(this)" style="padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 13.5px;">
                        <option value="">-- Choose Product from Catalog --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>" 
                                    data-name="<?= htmlspecialchars($p['name']) ?>" 
                                    data-code="<?= htmlspecialchars($p['product_code']) ?>" 
                                    data-price="<?= number_format($p['price'], 2) ?>" 
                                    data-saleprice="<?= number_format((float)($p['sale_price'] ?? 0), 2) ?>" 
                                    data-image="<?= htmlspecialchars($p['image']) ?>" 
                                    data-slug="<?= htmlspecialchars($p['slug']) ?>">
                                <?= htmlspecialchars($p['product_code']) ?> - <?= htmlspecialchars($p['name']) ?> (<?= number_format($p['price'], 2) ?> BHD)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Email Subject Line -->
                <div class="form-group">
                    <label style="font-size: 12.5px; font-weight: 700; color: #1e293b;">Email Subject Line *</label>
                    <input type="text" name="subject" id="promoSubject" required placeholder="e.g. Exclusive Spotlight: Black Luxury Abaya | Dar Jana Fashion" style="padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 13.5px;">
                </div>

                <!-- Custom Promotional Message -->
                <div class="form-group">
                    <label style="font-size: 12.5px; font-weight: 700; color: #1e293b;">Custom Promotional Message / Discount Note (Optional)</label>
                    <textarea name="custom_message" id="promoCustomMessage" rows="3" placeholder="Enter optional announcement message for subscribers, e.g. 'Enjoy 15% off using promo code EID15 during checkout!'" style="padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 13.5px;"></textarea>
                </div>

                <!-- Live Product Preview Box -->
                <div id="productPreviewBox" style="display: none; background: #faf8f5; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-top: 14px;">
                    <div style="font-size: 11px; font-weight: 700; color: #c5a059; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 10px;">Selected Product Live Email Preview</div>
                    <div style="display: flex; gap: 16px; align-items: center;">
                        <img id="previewImage" src="" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <div style="flex: 1;">
                            <div id="previewCode" style="font-size: 11px; color: #c5a059; font-weight: 700;"></div>
                            <div id="previewName" style="font-size: 14px; font-weight: 700; color: #181818;"></div>
                            <div id="previewPrice" style="font-size: 14px; font-weight: 800; color: #181818; margin-top: 2px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closePromoModal()" style="background: #ffffff; border: 1px solid #cbd5e0; color: #475569; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer;">Cancel</button>
                <button type="submit" id="sendBtn" style="background: #181818; color: #ffffff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    ✉️ Broadcast Promotional Email
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
let dtTable = null;

$(document).ready(function() {
    if ($('#subscribersTable').length) {
        dtTable = $('#subscribersTable').DataTable({
            "pageLength": 15,
            "order": [[ 2, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": [0, 4] }
            ],
            "language": {
                "search": "Filter Subscribers:",
                "lengthMenu": "Show _MENU_ rows"
            }
        });
    }
});

function toggleSelectAll(master) {
    $('.sub-checkbox').prop('checked', master.checked);
    updateSelectedBadge();
}

function updateSelectedBadge() {
    const checked = $('.sub-checkbox:checked');
    const count = checked.length;
    $('#selectedCountBadge').text(count);
    $('#modalSelectedCount').text(count);
}

function openPromoModal() {
    updateSelectedBadge();
    const count = $('.sub-checkbox:checked').length;
    if (count > 0) {
        $('#targetAudienceSelected').prop('checked', true);
    } else {
        $('#targetAudienceAll').prop('checked', true);
    }
    $('#promoCampaignModal').css('display', 'flex');
}

function closePromoModal() {
    $('#promoCampaignModal').css('display', 'none');
}

function sendSinglePromo(email) {
    $('.sub-checkbox').prop('checked', false);
    $(`.sub-checkbox[value="${email}"]`).prop('checked', true);
    openPromoModal();
}

function sendSelectedPromo() {
    const count = $('.sub-checkbox:checked').length;
    if (count === 0) {
        Swal.fire({
            title: 'No Subscribers Selected',
            text: 'Please select at least one subscriber checkbox in the table.',
            icon: 'warning',
            confirmButtonColor: '#181818'
        });
        return;
    }
    openPromoModal();
}

function onProductSelectChange(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
        $('#productPreviewBox').hide();
        return;
    }

    const name = opt.dataset.name;
    const code = opt.dataset.code;
    const price = opt.dataset.price;
    const saleprice = parseFloat(opt.dataset.saleprice || 0);
    const image = opt.dataset.image;

    $('#promoSubject').val(`Exclusive Spotlight: ${name} | Dar Jana Fashion`);
    $('#previewName').text(name);
    $('#previewCode').text(`CODE: ${code}`);
    
    if (saleprice > 0 && saleprice < parseFloat(price)) {
        $('#previewPrice').html(`<span style="text-decoration:line-through; color:#94a3b8; font-size:12px; margin-right:6px;">${price} BHD</span> ${saleprice.toFixed(2)} BHD`);
    } else {
        $('#previewPrice').text(`${price} BHD`);
    }

    let imgUrl = image;
    if (imgUrl && !imgUrl.startsWith('http')) {
        imgUrl = window.BASE_URL + '/' + imgUrl.replace(/^\//, '');
    }
    $('#previewImage').attr('src', imgUrl.replace('/high/', '/tiny/'));
    $('#productPreviewBox').show();
}

function submitPromoCampaign(e) {
    e.preventDefault();
    const form = document.getElementById('promoCampaignForm');
    const formData = new FormData(form);

    const target = $('input[name="target_audience"]:checked').val();
    if (target === 'selected') {
        const selected = [];
        $('.sub-checkbox:checked').each(function() {
            selected.push($(this).val());
        });
        if (selected.length === 0) {
            Swal.fire({
                title: 'No Audience Selected',
                text: 'Please select at least one subscriber from the table.',
                icon: 'warning',
                confirmButtonColor: '#181818'
            });
            return;
        }
        selected.forEach(email => formData.append('selected_emails[]', email));
    }

    const sendBtn = $('#sendBtn');
    sendBtn.prop('disabled', true).html('⏳ Broadcasting Emails...');

    fetch('<?= BASE_URL ?>/admin/subscribers/send-promo', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        sendBtn.prop('disabled', false).html('✉️ Broadcast Promotional Email');
        if (data.success) {
            Swal.fire({
                title: data.sent_count > 0 ? 'Campaign Sent!' : 'Campaign Completed',
                text: data.message,
                icon: data.sent_count > 0 ? 'success' : 'info',
                confirmButtonColor: '#181818'
            });
            closePromoModal();
        } else {
            Swal.fire({
                title: 'Campaign Error',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#181818'
            });
        }
    })
    .catch(err => {
        sendBtn.prop('disabled', false).html('✉️ Broadcast Promotional Email');
        Swal.fire({
            title: 'Request Failed',
            text: 'An error occurred while sending campaign emails.',
            icon: 'error',
            confirmButtonColor: '#181818'
        });
    });
}
</script>
</div>
</body>
</html>
