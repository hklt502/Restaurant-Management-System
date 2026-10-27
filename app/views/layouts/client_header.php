<?php $activePage = $activePage ?? ''; ?>
  <!-- ===================== THANH MENU ===================== -->
  <div id="header">
    <input type="checkbox" id="nav-toggle" />

    <div class="header-inner">
      <a class="logo" href="index.php?page=trangchu">Nhóm 3</a>

      <div id="menu">
        <a class="<?= ($activePage === 'trangchu') ? 'active' : '' ?>" href="index.php?page=trangchu">Trang Chủ</a>
        <a class="<?= ($activePage === 'thucdon') ? 'active' : '' ?>" href="index.php?page=thucdon">Thực Đơn</a>
        <a class="<?= ($activePage === 'khuyenmai') ? 'active' : '' ?>" href="index.php?page=khuyenmai">Khuyến Mãi</a>
        <a class="menu-button <?= ($activePage === 'datban') ? 'active' : '' ?>" href="index.php?page=datban">Đặt Bàn</a>
      </div>

      <div style="display: flex; align-items: center; gap: 8px;">
        <?php if (!empty($_SESSION['customer'])): ?>
          <a class="header-user" href="index.php?page=canhan">👤 <?= htmlspecialchars($_SESSION['customer']['customerName']) ?></a>
          <a href="index.php?page=dangxuat" style="font-size: 0.82rem; color: #c9a84c; text-decoration: none; padding: 4px 8px; border: 1px solid rgba(201,168,76,0.3); border-radius: 4px;" title="Đăng xuất">Thoát</a>
        <?php else: ?>
          <a class="header-user" href="index.php?page=dangnhap">👤 Đăng Nhập</a>
        <?php endif; ?>
      </div>

      <label class="burger" for="nav-toggle">
        <span></span><span></span><span></span>
      </label>
    </div>

    <div class="mobile-menu">
      <a class="<?= ($activePage === 'trangchu') ? 'active' : '' ?>" href="index.php?page=trangchu">Trang Chủ</a>
      <a class="<?= ($activePage === 'thucdon') ? 'active' : '' ?>" href="index.php?page=thucdon">Thực Đơn</a>
      <a class="<?= ($activePage === 'khuyenmai') ? 'active' : '' ?>" href="index.php?page=khuyenmai">Khuyến Mãi</a>
      <a class="<?= ($activePage === 'datban') ? 'active' : '' ?>" href="index.php?page=datban">Đặt Bàn</a>
      <?php if (!empty($_SESSION['customer'])): ?>
        <a class="<?= ($activePage === 'canhan') ? 'active' : '' ?>" href="index.php?page=canhan">Trang Cá Nhân (<?= htmlspecialchars($_SESSION['customer']['customerName']) ?>)</a>
        <a class="<?= ($activePage === 'doimatkhau') ? 'active' : '' ?>" href="index.php?page=doimatkhau">Đổi Mật Khẩu</a>
        <a href="index.php?page=dangxuat">Đăng Xuất</a>
      <?php else: ?>
        <a class="<?= ($activePage === 'dangnhap') ? 'active' : '' ?>" href="index.php?page=dangnhap">Đăng Nhập</a>
        <a class="<?= ($activePage === 'dangky') ? 'active' : '' ?>" href="index.php?page=dangky">Đăng Ký</a>
      <?php endif; ?>
    </div>
  </div>
