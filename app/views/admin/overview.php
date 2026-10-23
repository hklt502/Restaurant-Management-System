<?php
$pageTitle = 'Tổng quan - Nhóm 3';
$pageCss = '../../../public/asset/css/admin/overview.css';
$activePage = 'overview';
include_once __DIR__ . '/../layouts/admin_head.php';
include_once __DIR__ . '/../layouts/admin_sidebar.php';
include_once __DIR__ . '/../layouts/admin_header.php';
?>

      <div class="stat-cards">
        <div class="stat-card">
          <div class="stat-card__header">
            <div class="stat-card__icon">
              <i class="bx bx-bar-chart-alt-2"></i>
            </div>
            <span class="stat-card__badge stat-card__badge--up">+12.5%</span>
          </div>
          <p class="stat-card__label">Doanh thu hôm nay</p>
          <p class="stat-card__value">45.600.000đ</p>
        </div>
        <div class="stat-card">
          <div class="stat-card__header">
            <div class="stat-card__icon"><i class="bx bx-clipboard"></i></div>
            <span class="stat-card__badge stat-card__badge--up">+2.5%</span>
          </div>
          <p class="stat-card__label">Tổng đơn hàng</p>
          <p class="stat-card__value">128</p>
        </div>
        <div class="stat-card">
          <div class="stat-card__header">
            <div class="stat-card__icon">
              <i class="bx bx-spreadsheet"></i>
            </div>
            <span class="stat-card__badge stat-card__badge--down">25%</span>
          </div>
          <p class="stat-card__label">Bàn đang hoạt động</p>
          <p class="stat-card__value">18/25</p>
        </div>
        <div class="stat-card">
          <div class="stat-card__header">
            <div class="stat-card__icon"><i class="bx bx-group"></i></div>
            <span class="stat-card__badge stat-card__badge--up">3.5%</span>
          </div>
          <p class="stat-card__label">Khách hàng mới</p>
          <p class="stat-card__value">42</p>
        </div>
      </div>

      <div class="content-grid">
        <div class="content-grid__left">
          <div class="chart-card">
            <div class="chart-card__header">
              <div>
                <h2 class="chart-card__title">Thống kê doanh thu theo giờ</h2>
                <p class="chart-card__subtitle">
                  Hoạt động thời gian thực của nhà hàng
                </p>
              </div>
              <div class="chart-card__date-picker">
                <i class="bx bx-calendar"></i><span>05/06/2026</span>
              </div>
            </div>
            <div class="bar-chart">
              <div class="bar-chart__bars">
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 35%"></div>
                  <span>10:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 50%"></div>
                  <span>11:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 65%"></div>
                  <span>12:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 80%"></div>
                  <span>13:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 55%"></div>
                  <span>14:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 30%"></div>
                  <span>15:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 25%"></div>
                  <span>16:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 90%"></div>
                  <span>17:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 70%"></div>
                  <span>18:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 95%"></div>
                  <span>19:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 60%"></div>
                  <span>20:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 45%"></div>
                  <span>21:00</span>
                </div>
                <div class="bar-chart__bar-group">
                  <div class="bar-chart__bar" style="height: 20%"></div>
                  <span>22:00</span>
                </div>
              </div>
            </div>
          </div>

          <div class="orders-card">
            <div class="orders-card__header">
              <h2>Đơn hàng mới nhất</h2>
              <a href="orders.php">Xem tất cả</a>
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
                <tr>
                  <td>1</td>
                  <td>Nguyễn Hữu An</td>
                  <td>Bàn 04</td>
                  <td>1,250,000đ</td>
                  <td>
                    <span class="badge badge--paid">ĐÃ THANH TOÁN</span>
                  </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Lê Thị Hương Giang</td>
                  <td>Bàn 05</td>
                  <td>550,000đ</td>
                  <td>
                    <span class="badge badge--serving">ĐANG PHỤC VỤ</span>
                  </td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Lê Thị Hương Giang</td>
                  <td>Bàn 05</td>
                  <td>550,000đ</td>
                  <td>
                    <span class="badge badge--serving">ĐANG PHỤC VỤ</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="content-grid__right">
          <div class="bestseller-card">
            <h2>Món ăn bán chạy nhất</h2>
            <div class="bestseller-list">
              <div class="bestseller-item">
                <div class="bestseller-item__img">
                  <img src="../../../public/asset/img/thitbo.png" alt="Món ăn">
                </div>
                <div class="bestseller-item__info">
                  <p>Bò Mỹ Thượng Hạng</p>
                  <span>142 lượt gọi</span>
                </div>
                <span class="bestseller-item__rank rank--1">#1</span>
              </div>
              <div class="bestseller-item">
                <div class="bestseller-item__img">
                  <img src="../../../public/asset/img/thitbo.png" alt="Món ăn">
                </div>
                <div class="bestseller-item__info">
                  <p>Sủi cảo</p>
                  <span>98 lượt gọi</span>
                </div>
                <span class="bestseller-item__rank rank--2">#2</span>
              </div>
              <div class="bestseller-item">
                <div class="bestseller-item__img">
                  <img src="../../../public/asset/img/thitbo.png" alt="Món ăn">
                </div>
                <div class="bestseller-item__info">
                  <p>Hải sản</p>
                  <span>85 lượt gọi</span>
                </div>
                <span class="bestseller-item__rank rank--3">#3</span>
              </div>
            </div>
          </div>

          <div class="warehouse-card">
            <h2>Cảnh báo kho hàng</h2>
            <div class="warehouse-list">
              <div class="warehouse-item">
                <div>
                  <p>Bắp cải xanh</p>
                  <span>Hết hàng | Kho: 0 / Định mức: 15</span>
                </div>
                <span class="badge badge--out">HẾT</span>
              </div>
              <div class="warehouse-item">
                <div>
                  <p>Món salad tươi mát</p>
                  <span>Hết hàng | Kho: 0 / Định mức: 8</span>
                </div>
                <span class="badge badge--out">HẾT</span>
              </div>
              <div class="warehouse-item">
                <div>
                  <p>Món mì xào hải sản</p>
                  <span>Sắp hết | Kho: 3 / Định mức: 12</span>
                </div>
                <span class="badge badge--low">SẮP HẾT</span>
              </div>
              <div class="warehouse-item">
                <div>
                  <p>Món thịt nướng BBQ</p>
                  <span>Sắp hết | Kho: 4 / Định mức: 10</span>
                </div>
                <span class="badge badge--low">SẮP HẾT</span>
              </div>
            </div>
          </div>
        </div>
      </div>

<?php
include_once __DIR__ . '/../layouts/admin_footer.php';
?>

