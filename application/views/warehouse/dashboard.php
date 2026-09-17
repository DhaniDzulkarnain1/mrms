<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Dashboard - MRMS</title>
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
            <h2>RFI Requests to Check</h2>
        </div>

        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>RFI Number</th>
                        <th>Project Name</th>
                        <th>Requester</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rfi_list)): ?>
                        <?php foreach ($rfi_list as $rfi): ?>
                            <tr>
                                <td><?php echo $rfi->rfi_number; ?></td>
                                <td><?php echo $rfi->project_name; ?></td>
                                <td>Production User #<?php echo $rfi->requester_id; ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($rfi->submitted_at)); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $rfi->status; ?>">
                                        <?php echo ucfirst($rfi->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo base_url('warehouse/check_rfi/' . $rfi->id); ?>" 
                                       class="btn btn-sm btn-primary">Check Materials</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No submitted RFI requests</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
