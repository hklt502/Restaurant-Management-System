<?php
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhóm 3 - Đăng nhập</title>

    <link rel="stylesheet"
          href="public/asset/css/admin/login.css">
</head>

<body class="login-page">

<div class="login-box">

    <div class="logo">Nhóm 3</div>

    <h1>Đăng nhập</h1>

    <p class="login-text">
        Đăng nhập vào hệ thống quản lý nhà hàng
    </p>

    <form method="POST"
          action="index.php?page=admin-login">

        <?php if (!empty($error)): ?>
            <p style="color:red;">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <label for="username">Tên đăng nhập</label>

        <input type="text"
               id="username"
               name="username"
               placeholder="Nhập tên đăng nhập"
               value="<?= htmlspecialchars($oldUsername ?? '', ENT_QUOTES, 'UTF-8') ?>"
               autocomplete="username"
               required>

        <label for="password">Mật khẩu</label>

        <input type="password"
               id="password"
               name="password"
               placeholder="Nhập mật khẩu"
               autocomplete="current-password"
               required>

        <button type="submit">Đăng nhập</button>

    </form>

</div>

</body>
</html>
