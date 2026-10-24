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
          <span class="user-name"><?= htmlspecialchars($_SESSION['customer']['customerName'] ?? 'Thành viên') ?></span>
          <span class="user-role">Hội viên Nhóm 3</span>
        </div>
      </div>

      <div class="account-menu">
        <a href="index.php?page=canhan">Thông Tin Cá Nhân</a>
        <a class="active" href="index.php?page=doimatkhau">Đổi Mật Khẩu</a>
        <a href="index.php?page=dangxuat">Đăng Xuất</a>
      </div>
    </div>

    <!-- ---------- Cột phải: form đổi mật khẩu ---------- -->
    <div class="account-content">
      <p class="eyebrow">Bảo mật tài khoản</p>

      <h2>Đổi Mật Khẩu</h2>
      <p class="account-note">Nên dùng mật khẩu từ 8 ký tự và không dùng lại mật khẩu cũ.</p>

      <?php if (!empty($success)): ?>
        <div style="background: #e6f4ea; color: #137333; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
          🎉 Đổi mật khẩu thành công!
        </div>
      <?php elseif (!empty($error)): ?>
        <div style="background: #fce8e6; color: #c5221f; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
          ⚠️ <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form class="form-vertical" action="index.php?page=doimatkhau" method="POST">
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
          <a class="button button-outline" href="index.php?page=canhan">Hủy</a>
        </div>
      </form>
    </div>

  </div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>
