<?php $activePage = $activePage ?? ''; ?>
  <!-- ===================== SIDEBAR ===================== -->
  <aside class="sidebar">
    <div class="sidebar__logo">
      <i class="bx bx-restaurant"></i>
      <span>Nhóm 3</span>
    </div>

    <nav class="sidebar__nav">
      <div class="sidebar__group">
        <p class="sidebar__group-title">CHUNG</p>
        <a href="overview.php" class="sidebar__link <?= ($activePage === 'overview') ? 'active' : '' ?>"><i class="bx bxs-dashboard"></i><span>Tổng quan</span></a>
      </div>
      <div class="sidebar__group">
        <p class="sidebar__group-title">VẬN HÀNH</p>
        <a href="menu.php" class="sidebar__link <?= ($activePage === 'menu') ? 'active' : '' ?>"><i class="bx bx-book-open"></i><span>Quản lý Menu</span></a>
        <a href="tables.php" class="sidebar__link <?= ($activePage === 'tables') ? 'active' : '' ?>"><i class="bx bx-table"></i><span>Quản lý bàn</span></a>
        <a href="orders.php" class="sidebar__link <?= ($activePage === 'orders') ? 'active' : '' ?>"><i class="bx bx-receipt"></i><span>Quản lý đơn hàng</span></a>
      </div>
      <div class="sidebar__group">
        <p class="sidebar__group-title">MARKETING & KHO</p>
        <a href="vouchers.php" class="sidebar__link <?= ($activePage === 'vouchers') ? 'active' : '' ?>"><i class="bx bx-purchase-tag"></i><span>Voucher & Khuyến mãi</span></a>
        <a href="warehouse.php" class="sidebar__link <?= ($activePage === 'warehouse') ? 'active' : '' ?>"><i class="bx bx-box"></i><span>Quản lý Kho</span></a>
      </div>
      <div class="sidebar__group">
        <p class="sidebar__group-title">HỆ THỐNG</p>
        <a href="users.php" class="sidebar__link <?= ($activePage === 'users') ? 'active' : '' ?>"><i class="bx bx-user"></i><span>Người dùng</span></a>
        <a href="statistics.php" class="sidebar__link <?= ($activePage === 'statistics') ? 'active' : '' ?>"><i class="bx bx-line-chart"></i><span>Thống kê</span></a>
      </div>
    </nav>

    <div class="sidebar__profile">
      <div class="sidebar__profile-avatar"></div>
      <div class="sidebar__profile-info">
        <p class="sidebar__profile-name">Hồ Kim Lưu Thống</p>
        <p class="sidebar__profile-role">Quản trị viên</p>
      </div>
      <button class="sidebar__profile-logout" type="button" title="Đăng xuất">
        <i class="bx bx-log-out"></i>
      </button>
    </div>
  </aside>
