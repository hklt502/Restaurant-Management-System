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

  <!-- Thông báo kết quả nhận voucher -->
  <?php if (!empty($message)): ?>
    <div class="client-alert client-alert--success">
      <span><?= htmlspecialchars($message) ?></span>
      <a href="index.php?page=canhan">Xem Ví Voucher →</a>
    </div>
  <?php elseif (!empty($error)): ?>
    <div class="client-alert client-alert--error">
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <!-- ===================== DANH SÁCH ƯU ĐÃI ===================== -->
  <div class="promo-list">
    <div class="dish-grid">
      <?php if (!empty($promotions)): ?>
        <?php foreach ($promotions as $promo): ?>
          <div class="promo">
            <div class="promo-image">
              <img src="<?= htmlspecialchars(!empty($promo['imageURL']) ? $promo['imageURL'] : 'https://images.unsplash.com/photo-1709548145082-04d0cde481d4?w=600&h=400&fit=crop&auto=format') ?>"
                alt="<?= htmlspecialchars($promo['title']) ?>" />
              <?php if (!empty($promo['badgeText'])): ?>
                <span class="badge"><?= htmlspecialchars($promo['badgeText']) ?></span>
              <?php endif; ?>
            </div>
            <div class="promo-body">
              <h3><?= htmlspecialchars($promo['title']) ?></h3>
              <p><?= htmlspecialchars($promo['description'] ?? '') ?></p>
              <div class="promo-foot">
                <span><?= htmlspecialchars($promo['expiryInfo'] ?? 'Hạn: 31/12/2026') ?></span>
                <button type="button" class="button button-gold btn-open-modal" data-target="modal-promo-<?= (int)$promo['promotionId'] ?>">
                  Xem Voucher (<?= count($promo['vouchers'] ?? []) ?>)
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; grid-column: 1 / -1; color: var(--text-muted, #888);">Hiện tại chưa có chương trình khuyến mãi nào mới.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- ===================== CỬA SỔ MODAL CHI TIẾT VOUCHER ===================== -->
  <?php if (!empty($promotions)): ?>
    <?php foreach ($promotions as $promo): ?>
      <div class="modal-overlay" id="modal-promo-<?= (int)$promo['promotionId'] ?>">
        <div class="modal-box">
          <button type="button" class="modal-box__close" aria-label="Đóng">&times;</button>
          
          <p class="modal-box__eyebrow"><?= htmlspecialchars($promo['badgeText'] ?? 'Chương trình ưu đãi') ?></p>
          <h2 class="modal-box__title"><?= htmlspecialchars($promo['title']) ?></h2>
          <p class="modal-box__desc">
            <?= htmlspecialchars($promo['description'] ?? '') ?> 
            <?php if (!empty($promo['expiryInfo'])): ?>
              · <strong><?= htmlspecialchars($promo['expiryInfo']) ?></strong>
            <?php endif; ?>
          </p>

          <h3 class="block-title" style="margin: 0 0 14px; font-size: 16px;">Voucher áp dụng cho chương trình này</h3>

          <?php if (!empty($promo['vouchers'])): ?>
            <div class="modal-voucher-list">
              <?php foreach ($promo['vouchers'] as $v): ?>
                <?php 
                  $isClaimed = in_array((int)$v['voucherId'], $claimedVoucherIds ?? []); 
                ?>
                <div class="modal-voucher-item <?= $isClaimed ? 'modal-voucher-item--claimed' : '' ?>">
                  <div class="modal-voucher-info">
                    <p class="modal-voucher-code"><?= htmlspecialchars($v['voucherCode']) ?></p>
                    <p class="modal-voucher-discount">
                      Giảm <?= $v['discountType'] === 'PERCENT' ? (float)$v['discountValue'] . '%' : number_format($v['discountValue'], 0, ',', '.') . '₫' ?>
                    </p>
                    <p class="modal-voucher-meta">
                      Đơn tối thiểu: <?= number_format($v['minOrderValue'], 0, ',', '.') ?>₫
                      <?php if (!empty($v['expiryDate'])): ?>
                        · Hạn: <?= date('d/m/Y', strtotime($v['expiryDate'])) ?>
                      <?php endif; ?>
                    </p>
                  </div>

                  <div class="modal-voucher-action">
                    <?php if (empty($_SESSION['customer'])): ?>
                      <a href="index.php?page=dangnhap" class="button button-outline" title="Đăng nhập tài khoản để nhận voucher">Đăng nhập</a>
                    <?php elseif ($isClaimed): ?>
                      <button type="button" class="button button-claimed" disabled>✓ Đã lưu ví</button>
                    <?php else: ?>
                      <form action="index.php?page=nhan-voucher" method="POST">
                        <input type="hidden" name="voucherId" value="<?= (int)$v['voucherId'] ?>">
                        <button type="submit" class="button button-gold">Lưu vào ví</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="account-empty">Chưa có mã voucher phát hành cho chương trình này.</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- ===================== KÊU GỌI ĐẶT BÀN ===================== -->
  <div class="cta">
    <p class="eyebrow">Đừng bỏ lỡ ưu đãi hôm nay</p>
    <h2>Đặt Bàn Ngay Để<br /><span class="accent">Nhận Ưu Đãi</span></h2>
    <div class="cta-buttons">
      <a class="button button-gold" href="index.php?page=datban">Đặt Bàn Ngay</a>
      <a class="button button-outline" href="tel:02838245678">Gọi 028 3824 5678</a>
    </div>
  </div>

  <script>
    // Mở Modal tương ứng khi bấm nút "Xem Voucher"
    document.querySelectorAll('.btn-open-modal').forEach(btn => {
      btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-target');
        const modal = document.getElementById(targetId);
        if (modal) {
          modal.classList.add('active');
          document.body.style.overflow = 'hidden';
        }
      });
    });

    // Đóng Modal khi bấm nút X hoặc bấm ra ngoài overlay
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      const closeBtn = overlay.querySelector('.modal-box__close');
      if (closeBtn) {
        closeBtn.addEventListener('click', () => {
          overlay.classList.remove('active');
          document.body.style.overflow = '';
        });
      }

      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          overlay.classList.remove('active');
          document.body.style.overflow = '';
        }
      });
    });

    // Phím Escape để đóng nhanh cửa sổ
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(overlay => {
          overlay.classList.remove('active');
        });
        document.body.style.overflow = '';
      }
    });
  </script>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>
