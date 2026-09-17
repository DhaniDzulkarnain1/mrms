<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check RFI - MRMS</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>MRMS - Warehouse</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $user['full_name']; ?></span>
                <a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-title">
            <h2>Check Materials Availability</h2>
            <a href="<?php echo base_url('warehouse/dashboard'); ?>" class="btn btn-warning">Back to Dashboard</a>
        </div>

        <div class="card">
            <?php if ($this->session->flashdata('error')): ?>
                <div style="background: #e74c3c; color: white; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <div class="card-header">
                <h3><?php echo $rfi->rfi_number; ?> - <?php echo $rfi->project_name; ?></h3>
            </div>

            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                            <th>No</th>
                            <th>Material Name</th>
                            <th>Quantity</th>
                            <th>UOM</th>
                            <th>Status</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php $no = 1; foreach ($items as $item): ?>
                                <tr id="row-<?php echo $item->id; ?>">
                                    <td>
                                        <?php if ($item->status == 'pending'): ?>
                                            <input type="checkbox" class="item-checkbox" value="<?php echo $item->id; ?>" onchange="updateBulkActions()">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $item->material_name; ?></td>
                                    <td><?php echo number_format($item->quantity, 2); ?></td>
                                    <td><?php echo $item->uom; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $item->status; ?>" id="status-badge-<?php echo $item->id; ?>">
                                            <?php echo str_replace('_', ' ', ucfirst($item->status)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span id="remark-text-<?php echo $item->id; ?>">
                                            <?php echo $item->remark ? $item->remark : '-'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($item->status == 'pending'): ?>
                                            <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $item->id; ?>, 'ready')">
                                                Mark Ready
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="updateStatus(<?php echo $item->id; ?>, 'not_ready')">
                                                Not Ready
                                            </button>
                                        <?php else: ?>
                                            <span style="color: #27ae60;">✓ Checked</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No items</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="bulkActions" style="margin-top: 20px; margin-bottom: 20px; display: none;">
                <button class="btn btn-success" onclick="bulkUpdateStatus('ready')">
                    Mark Selected as Ready
                </button>
                <button class="btn btn-danger" onclick="bulkUpdateStatus('not_ready')">
                    Mark Selected as Not Ready
                </button>
                <span id="selectedCount" style="margin-left: 15px; color: #666;"></span>
            </div>

            <div style="margin-top: 20px;">
                <?php
                $has_pending = false;
                foreach ($items as $item) {
                    if ($item->status === 'pending') {
                        $has_pending = true;
                        break;
                    }
                }
                ?>
                <?php if ($has_pending): ?>
                    <button class="btn btn-primary" disabled style="opacity: 0.5; cursor: not-allowed;" title="Please check all items first">
                        Complete Checking (Please check all items first)
                    </button>
                <?php else: ?>
                    <a href="<?php echo base_url('warehouse/complete_checking/' . $rfi->id); ?>"
                       class="btn btn-primary"
                       onclick="return confirm('Complete checking and mark this RFI as checked?')">
                        Complete Checking
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateBulkActions();
        }

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            if (checkboxes.length > 0) {
                bulkActions.style.display = 'block';
                selectedCount.textContent = checkboxes.length + ' item(s) selected';
            } else {
                bulkActions.style.display = 'none';
            }

            const selectAll = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
        }

        function bulkUpdateStatus(status) {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one item');
                return;
            }

            let remark = '';
            if (status === 'not_ready') {
                remark = prompt('Please enter remark (why not ready):');
                if (!remark) {
                    alert('Remark is required for Not Ready status');
                    return;
                }
            }

            const itemIds = Array.from(checkboxes).map(cb => cb.value);
            let completed = 0;

            itemIds.forEach(itemId => {
                fetch('<?php echo base_url('warehouse/update_item_status'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>&item_id=' + itemId + '&status=' + status + '&remark=' + encodeURIComponent(remark)
                })
                .then(response => response.json())
                .then(data => {
                    completed++;
                    if (data.success) {
                        const badge = document.getElementById('status-badge-' + itemId);
                        badge.className = 'badge badge-' + status;
                        badge.textContent = status === 'ready' ? 'Ready' : 'Not Ready';

                        const remarkText = document.getElementById('remark-text-' + itemId);
                        remarkText.textContent = remark || '-';

                        const row = document.getElementById('row-' + itemId);
                        const actionCell = row.cells[7];
                        actionCell.innerHTML = '<span style="color: #27ae60;">✓ Checked</span>';

                        const checkbox = row.querySelector('.item-checkbox');
                        if (checkbox) {
                            checkbox.remove();
                        }
                    } else {
                        console.error('Failed to update item ' + itemId + ':', data.message);
                    }

                    if (completed === itemIds.length) {
                        updateBulkActions();
                        alert('All selected items updated successfully!');

                        const allRows = document.querySelectorAll('tbody tr');
                        let allChecked = true;
                        allRows.forEach(row => {
                            const badge = row.querySelector('.badge');
                            if (badge && badge.textContent.trim() === 'Pending') {
                                allChecked = false;
                            }
                        });

                        if (allChecked) {
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating item ' + itemId + ':', error);
                    completed++;
                });
            });
        }

        function updateStatus(itemId, status) {
            let remark = '';
            if (status === 'not_ready') {
                remark = prompt('Please enter remark (why not ready):');
                if (!remark) {
                    alert('Remark is required for Not Ready status');
                    return;
                }
            }

            fetch('<?php echo base_url('warehouse/update_item_status'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: '<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>&item_id=' + itemId + '&status=' + status + '&remark=' + encodeURIComponent(remark)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('status-badge-' + itemId);
                    badge.className = 'badge badge-' + status;
                    badge.textContent = status === 'ready' ? 'Ready' : 'Not Ready';

                    const remarkText = document.getElementById('remark-text-' + itemId);
                    remarkText.textContent = remark || '-';

                    const row = document.getElementById('row-' + itemId);
                    const actionCell = row.cells[7];
                    actionCell.innerHTML = '<span style="color: #27ae60;">✓ Checked</span>';

                    const checkbox = row.querySelector('.item-checkbox');
                    if (checkbox) {
                        checkbox.remove();
                    }

                    alert('Status updated successfully!');

                    const allRows = document.querySelectorAll('tbody tr');
                    let allChecked = true;
                    allRows.forEach(row => {
                        const badge = row.querySelector('.badge');
                        if (badge && badge.textContent.trim() === 'Pending') {
                            allChecked = false;
                        }
                    });

                    if (allChecked) {
                        location.reload();
                    }
                } else {
                    alert('Failed to update status: ' + (data.message || 'Unknown error'));
                    console.error('Error details:', data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status: ' + error.message);
            });
        }
    </script>
</body>
</html>
