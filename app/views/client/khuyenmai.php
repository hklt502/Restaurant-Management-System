<?php
$pageTitle = 'Khuyến Mãi — Nhóm 3';
$activePage = 'khuyenmai';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';
?>

  <!-- ===================== PHẦN MỞ ĐẦU ===================== -->
  <div class="page-hero">
    <div class="page-hero-inner">
      <p class="eyebrow">Ưu đãi dành cho thực khách</p>
      <h1>Chương Trình<br /><span class="accent">Khuyến Mãi</span></h1>
      <p>Những ưu đãi hấp dẫn nhất dành riêng cho thực khách của Nhóm 3.</p>
    </div>
  </div>

  <!-- ===================== DANH SÁCH ƯU ĐÃI ===================== -->
  <div class="promo-list">
    <div class="dish-grid">

      <div class="promo">
        <div class="promo-image">
          <img src="https://images.unsplash.com/photo-1709548145082-04d0cde481d4?w=600&h=400&fit=crop&auto=format"
            alt="Khuyến mãi cho nhóm trên 5 người" />
          <span class="badge">Tặng voucher 100k</span>
        </div>
        <div class="promo-body">
          <h3>Cho nhóm trên 5 người</h3>
          <p>Tụ tập bạn bè, sum họp gia đình thêm đông vui cùng Nhóm 3.
            Nhận ngay voucher 100.000 VNĐ cho lần đặt bàn tiếp theo.</p>
          <div class="promo-foot">
            <span>Hạn: 31/12/2026</span>
            <a class="button button-gold" href="datban.php">Nhận ưu đãi</a>
          </div>
        </div>
      </div>

      <div class="promo">
        <div class="promo-image">
          <img src="https://images.unsplash.com/photo-1650490632010-e18cbb694dd1?w=600&h=400&fit=crop&auto=format"
            alt="Khuyến mãi cho hội viên mới" />
          <span class="badge">Giảm 15% tổng bill</span>
        </div>
        <div class="promo-body">
          <h3>Dành cho hội viên mới</h3>
          <p>Đăng ký thành viên Nhóm 3 lần đầu để nhận ngay voucher giảm trực tiếp
            15% trên tổng hóa đơn.</p>
          <div class="promo-foot">
            <span>Khách hàng mới</span>
            <a class="button button-gold" href="dangky.php">Đăng ký ngay</a>
          </div>
        </div>
      </div>

      <div class="promo">
        <div class="promo-image">
          <img src="https://images.unsplash.com/photo-1555126634-323283e090fa?w=600&h=400&fit=crop&auto=format"
            alt="Khuyến mãi gói Diamond" />
          <span class="badge">Đi 4 tính 3</span>
        </div>
        <div class="promo-body">
          <h3>Đồng hành cùng gói Diamond</h3>
          <p>Trải nghiệm thực đơn thượng hạng với tôm hùm và sườn bò Mỹ.
            Ưu đãi đi 4 người chỉ tính tiền cho 3 suất buffet.</p>
          <div class="promo-foot">
            <span>Thứ 2 – Thứ 6</span>
            <a class="button button-gold" href="datban.php">Nhận ưu đãi</a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- ===================== ĐIỀU KHOẢN CHUNG ===================== -->
  <div class="terms">
    <div class="terms-inner">
      <h3>ⓘ Điều khoản chung</h3>
      <ul>
        <li>Các chương trình khuyến mãi không áp dụng đồng thời với nhau và với các chương trình ưu đãi khác.</li>
        <li>Ưu đãi không có giá trị quy đổi thành tiền mặt.</li>
        <li>Vui lòng thông báo cho nhân viên phục vụ trước khi gọi món hoặc thanh toán.</li>
        <li>Nhà hàng có quyền điều chỉnh điều khoản chương trình mà không cần thông báo trước.</li>
      </ul>
    </div>
  </div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>

