<?php
$pageTitle = 'Đổi Mật Khẩu — Nhóm 3';
$activePage = 'doimatkhau';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';
?>

  <!-- ===================== CỘT TRÁI + NỘI DUNG ===================== -->
  <div class="account">

    <!-- ---------- Cột trái: thông tin thành viên và menu dọc ---------- -->
    <div>
      <div class="user-card">
        <span class="avatar">👤</span>
        <div>
          <span class="user-name">Nguyễn Văn A</span>
          <span class="user-role">Thành viên</span>
        </div>
      </div>

      <div class="account-menu">
        <a href="canhan.php">Thông Tin Cá Nhân</a>
        <a class="active" href="doimatkhau.php">Đổi Mật Khẩu</a>
        <a href="dangnhap.php">Đăng Xuất</a>
      </div>
    </div>

    <!-- ---------- Cột phải: form đổi mật khẩu ---------- -->
    <div class="account-content">
      <p class="eyebrow">Bảo mật tài khoản</p>

      <h2>Đổi Mật Khẩu</h2>
      <p class="account-note">Nên dùng mật khẩu từ 8 ký tự và không dùng lại mật khẩu cũ.</p>

      <form class="form-vertical">
        <div class="field">
          <label for="old-pass">Mật khẩu cũ *</label>
          <input type="password" id="old-pass" name="old-pass" placeholder="••••••••" required />
        </div>

        <div class="field">
          <label for="new-pass">Mật khẩu mới *</label>
          <input type="password" id="new-pass" name="new-pass" placeholder="••••••••" required />
        </div>

        <div class="field">
          <label for="confirm-pass">Nhập lại mật khẩu mới *</label>
          <input type="password" id="confirm-pass" name="confirm-pass" placeholder="••••••••" required />
        </div>

        <div class="button-group">
          <button type="submit" class="button button-gold">Đổi Mật Khẩu</button>
          <a class="button button-outline" href="canhan.php">Hủy</a>
        </div>
      </form>
    </div>

  </div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>

