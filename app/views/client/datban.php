<?php
$pageTitle = 'Đặt Bàn — Nhóm 3';
$activePage = 'datban';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';
?>

<!-- ===================== PHẦN MỞ ĐẦU ===================== -->
<div class="page-hero">
  <div class="page-hero-inner">
    <p class="eyebrow">Đặt bàn trực tuyến</p>
    <h1>Giữ Một Chỗ<br /><span class="accent">Cho Buổi Tối Nay</span></h1>
  </div>
</div>

<!-- ===================== NỘI DUNG ===================== -->
<div class="booking">

  <!-- ---------- Cột trái: form đặt bàn ---------- -->
  <div class="form-card">
    <h2>Thông tin đặt bàn</h2>
    <p class="form-note">Chúng tôi sẽ gọi lại xác nhận trong vòng 15 phút.</p>

    <?php if (!empty($success)): ?>
      <div style="background: #e6f4ea; color: #137333; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
        🎉 Đặt bàn thành công! Nhà hàng sẽ liên hệ qua điện thoại để xác nhận sớm nhất.
      </div>
    <?php elseif (!empty($error)): ?>
      <div style="background: #fce8e6; color: #c5221f; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
        ⚠️ <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="index.php?page=datban" method="POST">
      <div class="form-grid">
        <div class="field">
          <label for="name">Họ và tên *</label>
          <input type="text" id="name" name="name" placeholder="Nguyễn Văn A" required value="<?php if (!empty($_SESSION['customer'])) echo $_SESSION['customer']['customerName'] ?>" />
        </div>

        <div class="field">
          <label for="phone">Số điện thoại *</label>
          <input type="tel" id="phone" name="phone" placeholder="0901 234 567" required value="<?php if (!empty($_SESSION['customer'])) echo $_SESSION['customer']['customerPhone'] ?>" />
        </div>

        <div class="field">
          <label for="date">Ngày *</label>
          <input type="date" id="date" name="date" required "/>
        </div>

        <div class=" field">
          <label for="time">Giờ *</label>
          <input type="time" id="time" name="time" value="19:00" required />
        </div>

        <div class="field">
          <label for="guests">Số khách *</label>
          <select id="guests" name="guests" required>
            <option value="1">1 khách</option>
            <option value="2" selected>2 khách</option>
            <option value="3">3 khách</option>
            <option value="4">4 khách</option>
            <option value="5">5 khách</option>
            <option value="6">6 khách</option>
            <option value="8">7 – 8 khách</option>
            <option value="10">9 – 10 khách</option>
            <option value="20">Trên 10 khách (tiệc)</option>
          </select>
        </div>

        <div class="field">
          <label for="branch">Chi nhánh</label>
          <select id="branch" name="branch">
            <option value="le-loi" selected>Lê Lợi — Quận 1</option>
            <option value="ben-thanh">Bến Thành — Quận 1</option>
            <option value="thao-dien">Thảo Điền — TP. Thủ Đức</option>
          </select>
        </div>

        <div class="field field-full">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="ban@example.com" />
          <span class="field-note">Không bắt buộc — dùng để gửi xác nhận đặt bàn.</span>
        </div>

        <div class="field field-full">
          <label for="note">Ghi chú</label>
          <textarea id="note" name="note"
            placeholder="Dị ứng thực phẩm, bàn gần cửa sổ, sinh nhật, xe lăn…"></textarea>
        </div>

        <div class="form-foot">
          <p>Cần hỗ trợ gấp? Gọi <span>028 3824 5678</span></p>
          <button type="submit" class="button button-gold">Xác Nhận Đặt Bàn</button>
        </div>

      </div>
    </form>
  </div>

  <!-- ---------- Cột phải: thông tin ---------- -->
  <div>

    <div class="info-card">
      <h2>Thông tin liên hệ</h2>

      <div class="info-row">
        <span class="info-key">Địa chỉ</span>
        <span class="info-value">123 Đường Lê Lợi, Quận 1, TP.HCM</span>
      </div>

      <div class="info-row">
        <span class="info-key">Giờ mở cửa</span>
        <span class="info-value">10:00 – 22:00<br />Tất cả các ngày trong tuần</span>
      </div>

      <div class="info-row">
        <span class="info-key">Điện thoại</span>
        <span class="info-value"><a href="tel:02838245678">028 3824 5678</a></span>
      </div>

      <div class="info-row">
        <span class="info-key">Email</span>
        <span class="info-value"><a href="mailto:hello@Nhóm 3.vn">hello@Nhóm 3.vn</a></span>
      </div>
    </div>

    <div class="info-card">
      <h2>Lưu ý khi đặt bàn</h2>
      <ul>
        <li>Bàn được giữ tối đa 15 phút kể từ giờ đã hẹn.</li>
        <li>Nhóm trên 10 khách vui lòng đặt trước 24 giờ để nhà hàng sắp xếp không gian riêng.</li>
        <li>Khung 18:00 – 20:00 cuối tuần thường kín; nên đặt trước 2 – 3 ngày.</li>
        <li>Có khu vực phòng riêng cho tiệc, vui lòng ghi rõ trong phần ghi chú.</li>
        <li>Nhà hàng phục vụ được yêu cầu ăn chay và các món ít cay.</li>
      </ul>
    </div>

  </div>

</div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>