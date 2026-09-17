<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Dashboard - MRMS</title>
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
            <h2>My RFI Requests</h2>
            <a href="<?php echo base_url('production/create_rfi'); ?>" class="btn btn-primary">Create New RFI</a>
        </div>

        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th>RFI Number</th>
                        <th>Project Name</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rfi_list)): ?>
                        <?php foreach ($rfi_list as $rfi): ?>
                            <tr>
                                <td><?php echo $rfi->rfi_number; ?></td>
                                <td><?php echo $rfi->project_name; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $rfi->status; ?>">
                                        <?php echo ucfirst($rfi->status); ?>
                                    </span>
                                </td>
                                <td><?php echo $rfi->submitted_at ? date('d M Y H:i', strtotime($rfi->submitted_at)) : '-'; ?></td>
                                <td>
                                    <a href="<?php echo base_url('production/view_rfi/' . $rfi->id); ?>" class="btn btn-sm btn-primary">View</a>
                                    <?php if ($rfi->status == 'draft'): ?>
                                        <a href="<?php echo base_url('production/edit_rfi/' . $rfi->id); ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="<?php echo base_url('production/submit_rfi/' . $rfi->id); ?>"
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Submit this RFI?')">Submit</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No RFI requests yet. Create your first one!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
