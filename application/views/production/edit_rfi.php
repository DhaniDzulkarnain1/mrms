<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit RFI - MRMS</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>MRMS - Production</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $user['full_name']; ?></span>
                <a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-title">
            <h2>Edit RFI - <?php echo $rfi->rfi_number; ?></h2>
            <a href="<?php echo base_url('production/dashboard'); ?>" class="btn btn-warning">Back to Dashboard</a>
        </div>

        <div class="card">
            <form method="post" action="<?php echo base_url('production/update_rfi/' . $rfi->id); ?>" id="rfiForm">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                    <label for="project_name">Project Name *</label>
                    <input type="text" class="form-control" id="project_name" name="project_name" value="<?php echo $rfi->project_name; ?>" required>
                </div>

                <h3>Materials</h3>
                <div id="materialsContainer">
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <div class="material-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end;">
                                <input type="hidden" name="item_ids[]" value="<?php echo $item->id; ?>">
                                <div style="flex: 2;">
                                    <label>Material Name *</label>
                                    <input type="text" class="form-control" name="materials[]" value="<?php echo $item->material_name; ?>" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>Quantity *</label>
                                    <input type="number" class="form-control" name="quantities[]" step="0.01" value="<?php echo $item->quantity; ?>" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>UOM *</label>
                                    <select class="form-control" name="uoms[]" required>
                                        <option value="">Select UOM</option>
                                        <option value="PCS" <?php echo $item->uom == 'PCS' ? 'selected' : ''; ?>>PCS</option>
                                        <option value="MTR" <?php echo $item->uom == 'MTR' ? 'selected' : ''; ?>>MTR</option>
                                        <option value="BOX" <?php echo $item->uom == 'BOX' ? 'selected' : ''; ?>>BOX</option>
                                        <option value="KG" <?php echo $item->uom == 'KG' ? 'selected' : ''; ?>>KG</option>
                                        <option value="LTR" <?php echo $item->uom == 'LTR' ? 'selected' : ''; ?>>LTR</option>
                                        <option value="SET" <?php echo $item->uom == 'SET' ? 'selected' : ''; ?>>SET</option>
                                        <option value="UNIT" <?php echo $item->uom == 'UNIT' ? 'selected' : ''; ?>>UNIT</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteItem(this, <?php echo $item->id; ?>)">Delete</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="button" class="btn btn-success btn-sm" onclick="addMaterialRow()">+ Add Material</button>
                <br><br>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Update RFI</button>
                    <a href="<?php echo base_url('production/dashboard'); ?>" class="btn btn-warning">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addMaterialRow() {
            const container = document.getElementById('materialsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'material-row';
            newRow.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end;';
            newRow.innerHTML = `
                <input type="hidden" name="item_ids[]" value="">
                <div style="flex: 2;">
                    <input type="text" class="form-control" name="materials[]" placeholder="Material Name" required>
                </div>
                <div style="flex: 1;">
                    <input type="number" class="form-control" name="quantities[]" step="0.01" placeholder="Qty" required>
                </div>
                <div style="flex: 1;">
                    <select class="form-control" name="uoms[]" required>
                        <option value="">Select UOM</option>
                        <option value="PCS">PCS</option>
                        <option value="MTR">MTR</option>
                        <option value="BOX">BOX</option>
                        <option value="KG">KG</option>
                        <option value="LTR">LTR</option>
                        <option value="SET">SET</option>
                        <option value="UNIT">UNIT</option>
                    </select>
                </div>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Delete</button>
            `;
            container.appendChild(newRow);
        }

        function deleteItem(btn, itemId) {
            if (!confirm('Delete this item?')) {
                return;
            }

            if (itemId) {
                const formData = new FormData();
                formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

                fetch('<?php echo base_url('production/delete_item/'); ?>' + itemId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        btn.parentElement.remove();
                    } else {
                        alert('Failed to delete item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting item');
                });
            } else {
                btn.parentElement.remove();
            }
        }
    </script>
</body>
</html>
