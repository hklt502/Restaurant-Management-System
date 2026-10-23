<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $pageTitle ?? 'Quản lý Hệ thống - Nhóm 3' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <!-- Mỗi trang admin sẽ nạp file CSS riêng thông qua biến $pageCss nếu cần -->
  <?php if (!empty($pageCss)): ?>
    <link rel="stylesheet" href="<?= $pageCss ?>" />
  <?php endif; ?>
</head>

<body>

