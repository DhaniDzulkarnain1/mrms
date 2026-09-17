<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MRMS</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>MRMS</h1>
            <h2>Login</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo base_url('auth/login'); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <?php /*
            <div class="login-info">
                <p><strong>Demo Accounts:</strong></p>
                <p>Production: <code>production1</code> / <code>password123</code></p>
                <p>Warehouse: <code>warehouse1</code> / <code>password123</code></p>
            </div>
            */ ?>
        </div>
    </div>
</body>
</html>
