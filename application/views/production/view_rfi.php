<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View RFI - MRMS</title>
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
            <h2>RFI Detail</h2>
            <a href="<?php echo base_url('production/dashboard'); ?>" class="btn btn-warning">Back to Dashboard</a>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><?php echo $rfi->rfi_number; ?></h3>
            </div>

            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 10px; width: 200px;"><strong>Project Name:</strong></td>
                    <td style="padding: 10px;"><?php echo $rfi->project_name; ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><strong>Status:</strong></td>
                    <td style="padding: 10px;">
                        <span class="badge badge-<?php echo $rfi->status; ?>">
                            <?php echo ucfirst($rfi->status); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><strong>Created:</strong></td>
                    <td style="padding: 10px;"><?php echo date('d M Y H:i', strtotime($rfi->created_at)); ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><strong>Submitted:</strong></td>
                    <td style="padding: 10px;"><?php echo $rfi->submitted_at ? date('d M Y H:i', strtotime($rfi->submitted_at)) : '-'; ?></td>
                </tr>
            </table>

            <h3>Materials</h3>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Material Name</th>
                            <th>Quantity</th>
                            <th>UOM</th>
                            <th>Status</th>
                            <th>Remark</th>
                            <th>Checked At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php $no = 1; foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $item->material_name; ?></td>
                                    <td><?php echo number_format($item->quantity, 2); ?></td>
                                    <td><?php echo $item->uom; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $item->status; ?>">
                                            <?php echo str_replace('_', ' ', ucfirst($item->status)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $item->remark ? $item->remark : '-'; ?></td>
                                    <td><?php echo $item->checked_at ? date('d M Y H:i', strtotime($item->checked_at)) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No items</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($rfi->status == 'draft'): ?>
                <div style="margin-top: 20px;">
                    <a href="<?php echo base_url('production/submit_rfi/' . $rfi->id); ?>" 
                       class="btn btn-success"
                       onclick="return confirm('Submit this RFI? You cannot edit after submission.')">
                        Submit RFI
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
