<?php
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-overview');
    exit;
}

$pageTitle  = 'Tổng quan - Nhóm 3';
$pageCss    = 'public/asset/css/admin/overview.css';
$activePage = 'overview';

$selectedDate  = $selectedDate ?? '';
$revenue       = $revenue ?? 0;
$totalOrders   = $totalOrders ?? 0;
$occupiedCount = $occupiedCount ?? 0;
$totalTables   = $totalTables ?? 0;
$tablePercent  = $tablePercent ?? 0;
$customerCount = $customerCount ?? 0;
$hourlyHeights = $hourlyHeights ?? array_fill(10, 13, 10);
$hourlyVal     = $hourlyVal ?? array_fill(10, 13, 0);
$recentOrders  = $recentOrders ?? [];
$featuredFoods = $featuredFoods ?? [];
$stockAlerts   = $stockAlerts ?? [];

include_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="stat-cards">
  <div class="stat-card">
    <div class="stat-card__header">
      <div class="stat-card__icon">
        <i class="bx bx-bar-chart-alt-2"></i>
      </div>
    </div>
    <p class="stat-card__label">Doanh thu hôm nay</p>
    <p class="stat-card__value"><?= number_format($revenue, 0, ',', '.') ?>đ</p>
  </div>
  <div class="stat-card">
    <div class="stat-card__header">
      <div class="stat-card__icon"><i class="bx bx-clipboard"></i></div>
      <span class="stat-card__badge stat-card__badge--up">+2.5%</span>
    </div>
    <p class="stat-card__label">Đơn hàng hôm nay</p>
    <p class="stat-card__value"><?= $totalOrders ?></p>
  </div>
  <div class="stat-card">
    <div class="stat-card__header">
      <div class="stat-card__icon">
        <i class="bx bx-spreadsheet"></i>
      </div>
      <span class="stat-card__badge <?= $tablePercent >= 50 ? 'stat-card__badge--up' : 'stat-card__badge--down' ?>"><?= $tablePercent ?>%</span>
    </div>
    <p class="stat-card__label">Bàn đang hoạt động</p>
    <p class="stat-card__value"><?= $occupiedCount ?>/<?= $totalTables ?></p>
  </div>
  <div class="stat-card">
    <div class="stat-card__header">
      <div class="stat-card__icon"><i class="bx bx-group"></i></div>
      <span class="stat-card__badge stat-card__badge--up">Active</span>
    </div>
    <p class="stat-card__label">Khách hàng mới</p>
    <p class="stat-card__value"><?= $customerCount ?></p>
  </div>
</div>

<div class="content-grid">
  <div class="content-grid__left">
    <div class="chart-card">
      <div class="chart-card__header">
        <div>
          <h2 class="chart-card__title">Thống kê doanh thu theo giờ</h2>
          <p class="chart-card__subtitle">
            Hoạt động thời gian thực của nhà hàng từ cơ sở dữ liệu
          </p>
        </div>
        <form method="GET" action="index.php" style="display:flex;align-items:center;gap:8px;">
          <input type="hidden" name="page" value="admin-overview">
          <label class="chart-card__date-picker" style="margin:0;">
            <i class="bx bx-calendar"></i>
            <input type="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>"
              onchange="this.form.submit()"
              style="border:none;background:transparent;font-family:inherit;font-size:0.86rem;color:var(--text2);outline:none;cursor:pointer;">
          </label>
          <?php if (!empty($selectedDate)): ?>
            <a href="index.php?page=admin-overview" style="font-size:0.8rem;color:var(--primary-400);font-weight:600;">Tất cả</a>
          <?php endif; ?>
        </form>
      </div>
      <div class="bar-chart">
        <div class="bar-chart__bars">
          <?php for ($h = 10; $h <= 22; $h++): ?>
            <div class="bar-chart__bar-group" title="<?= $h ?>:00 - <?= number_format($hourlyVal[$h] ?? 0, 0, ',', '.') ?>đ">
              <div class="bar-chart__bar" style="height: <?= $hourlyHeights[$h] ?? 10 ?>%"></div>
              <span><?= $h ?>:00</span>
            </div>
          <?php endfor; ?>
        </div>
      </div>
    </div>

    <div class="orders-card">
      <div class="orders-card__header">
        <h2>Đơn hàng mới nhất</h2>
        <a href="index.php?page=admin-orders">Xem tất cả</a>
      </div>
      <table class="orders-table">
        <thead>
          <tr>
            <th>MÃ ĐƠN</th>
            <th>KHÁCH HÀNG</th>
            <th>SỐ BÀN</th>
            <th>TỔNG TIỀN</th>
            <th>TRẠNG THÁI</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recentOrders)): ?>
            <?php foreach ($recentOrders as $ord): ?>
              <?php
              $orderCode = 'DH-' . str_pad((string)$ord['orderId'], 4, '0', STR_PAD_LEFT);
              $custName  = !empty($ord['customerName']) ? $ord['customerName'] : 'Khách lẻ';
              $tableName = !empty($ord['tableNumber']) ? 'Bàn ' . $ord['tableNumber'] : 'Mang về';
              $st        = strtoupper(trim($ord['status'] ?? 'OPEN'));
              ?>
              <tr data-search="<?= htmlspecialchars(mb_strtolower($orderCode . ' ' . $custName . ' ' . $tableName, 'UTF-8')) ?>">
                <td><strong><?= htmlspecialchars($orderCode) ?></strong></td>
                <td><?= htmlspecialchars($custName) ?></td>
                <td><?= htmlspecialchars($tableName) ?></td>
                <td><?= number_format((float)($ord['totalAmount'] ?? 0), 0, ',', '.') ?>đ</td>
                <td>
                  <?php if ($st === 'PAID'): ?>
                    <span class="badge badge--paid">ĐÃ THANH TOÁN</span>
                  <?php elseif ($st === 'CANCELLED'): ?>
                    <span class="badge badge--out">ĐÃ HỦY</span>
                  <?php elseif ($st === 'OPEN'): ?>
                    <span class="badge badge--serving">ĐANG CHẾ BIẾN</span>
                  <?php elseif ($st === 'PENDING' || $st === 'PENDING_PAY'): ?>
                    <span class="badge badge--low">CHỜ THANH TOÁN</span>
                  <?php else: ?>
                    <span class="badge badge--serving">ĐANG PHỤC VỤ</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" style="text-align:center;color:var(--text3);">Chưa có đơn hàng nào trong cơ sở dữ liệu.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="content-grid__right">
    <div class="bestseller-card">
      <h2>Món ăn nổi bật</h2>
      <div class="bestseller-list">
        <?php if (!empty($featuredFoods)): ?>
          <?php foreach ($featuredFoods as $idx => $food): ?>
            <?php
            $rank   = $idx + 1;
            $imgSrc = !empty($food['imageURL']) ? ltrim($food['imageURL'], '/') : 'public/asset/img/thitbo.png';
            ?>
            <div class="bestseller-item">
              <div class="bestseller-item__img">
                <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($food['foodName']) ?>" onerror="this.src='public/asset/img/thitbo.png'">
              </div>
              <div class="bestseller-item__info">
                <p><?= htmlspecialchars($food['foodName']) ?></p>
                <span><?= htmlspecialchars($food['categoryName'] ?? '') ?> • <?= number_format((float)$food['foodPrice'], 0, ',', '.') ?>đ</span>
              </div>
              <span class="bestseller-item__rank rank--<?= $rank ?>">#<?= $rank ?></span>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color:var(--text3);font-size:0.86rem;">Chưa có dữ liệu món nổi bật.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="warehouse-card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2 style="margin-bottom:0;">Cảnh báo kho hàng</h2>
        <a href="index.php?page=admin-warehouse" style="font-size:0.82rem;font-weight:600;color:var(--primary-300);">Chi tiết kho</a>
      </div>
      <div class="warehouse-list">
        <?php if (!empty($stockAlerts)): ?>
          <?php foreach ($stockAlerts as $ing): ?>
            <?php
            $qty   = (float)$ing['quantityInStock'];
            $min   = (float)$ing['minQuantity'];
            $unit  = $ing['unit'];
            $isOut = ($qty <= 0);
            ?>
            <div class="warehouse-item">
              <div>
                <p><?= htmlspecialchars($ing['ingredientName']) ?></p>
                <span><?= $isOut ? 'Hết hàng' : 'Sắp hết' ?> | Kho: <?= rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.') ?> <?= htmlspecialchars($unit) ?> / Định mức: <?= rtrim(rtrim(number_format($min, 2, '.', ''), '0'), '.') ?> <?= htmlspecialchars($unit) ?></span>
              </div>
              <span class="badge <?= $isOut ? 'badge--out' : 'badge--low' ?>"><?= $isOut ? 'HẾT' : 'SẮP HẾT' ?></span>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color:var(--green);font-size:0.86rem;font-weight:600;">Tất cả nguyên liệu đều đủ định mức tồn kho.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  const headerSearch = document.querySelector('.header__search input');
  if (headerSearch) {
    headerSearch.addEventListener('input', function() {
      const kw = this.value.trim().toLowerCase();
      document.querySelectorAll('.orders-table tbody tr[data-search]').forEach(row => {
        row.style.display = (!kw || row.dataset.search.includes(kw)) ? '' : 'none';
      });
    });
  }
</script>

<!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
</main>
</div>

</body>

</html>
