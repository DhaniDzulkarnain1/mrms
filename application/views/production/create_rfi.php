<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create RFI - MRMS</title>
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
            <h2>Create New RFI</h2>
            <a href="<?php echo base_url('production/dashboard'); ?>" class="btn btn-warning">Back to Dashboard</a>
        </div>

        <div class="card">
            <form method="post" action="<?php echo base_url('production/save_rfi'); ?>" id="rfiForm">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                    <label for="project_name">Project Name *</label>
                    <input type="text" class="form-control" id="project_name" name="project_name" required>
                </div>

                <h3>Materials</h3>
                <div id="materialsContainer">
                    <div class="material-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <div style="flex: 2;">
                            <label>Material Name *</label>
                            <input type="text" class="form-control" name="materials[]" required>
                        </div>
                        <div style="flex: 1;">
                            <label>Quantity *</label>
                            <input type="number" class="form-control" name="quantities[]" step="0.01" required>
                        </div>
                        <div style="flex: 1;">
                            <label>UOM *</label>
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
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-sm" onclick="addMaterialRow()">+ Add Material</button>
                <br><br>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Save as Draft</button>
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
                <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Remove</button>
            `;
            container.appendChild(newRow);
        }
    </script>
</body>
</html>
