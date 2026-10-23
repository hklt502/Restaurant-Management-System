<?php $activePage = $activePage ?? ''; ?>
  <!-- ===================== THANH MENU ===================== -->
  <div id="header">
    <input type="checkbox" id="nav-toggle" />

    <div class="header-inner">
      <a class="logo" href="trangchu.php">Nhóm 3</a>

      <div id="menu">
        <a class="<?= ($activePage === 'trangchu') ? 'active' : '' ?>" href="trangchu.php">Trang Chủ</a>
        <a class="<?= ($activePage === 'thucdon') ? 'active' : '' ?>" href="thucdon.php">Thực Đơn</a>
        <a class="<?= ($activePage === 'khuyenmai') ? 'active' : '' ?>" href="khuyenmai.php">Khuyến Mãi</a>
        <a class="menu-button <?= ($activePage === 'datban') ? 'active' : '' ?>" href="datban.php">Đặt Bàn</a>
      </div>

      <a class="header-user" href="canhan.php">👤 Nguyễn Văn A</a>

      <label class="burger" for="nav-toggle">
        <span></span><span></span><span></span>
      </label>
    </div>

    <div class="mobile-menu">
      <a class="<?= ($activePage === 'trangchu') ? 'active' : '' ?>" href="trangchu.php">Trang Chủ</a>
      <a class="<?= ($activePage === 'thucdon') ? 'active' : '' ?>" href="thucdon.php">Thực Đơn</a>
      <a class="<?= ($activePage === 'khuyenmai') ? 'active' : '' ?>" href="khuyenmai.php">Khuyến Mãi</a>
      <a class="<?= ($activePage === 'datban') ? 'active' : '' ?>" href="datban.php">Đặt Bàn</a>
      <a class="<?= ($activePage === 'dangnhap') ? 'active' : '' ?>" href="dangnhap.php">Đăng Nhập</a>
      <a class="<?= ($activePage === 'dangky') ? 'active' : '' ?>" href="dangky.php">Đăng Ký</a>
      <a class="<?= ($activePage === 'canhan') ? 'active' : '' ?>" href="canhan.php">Trang Cá Nhân</a>
      <a class="<?= ($activePage === 'doimatkhau') ? 'active' : '' ?>" href="doimatkhau.php">Đổi Mật Khẩu</a>
    </div>
  </div>
