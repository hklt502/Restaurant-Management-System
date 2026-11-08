<?php
// Nếu truy cập trực tiếp file View trên trình duyệt -> chuyển hướng về Front Controller (OrderController::index)
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-orders');
    exit;
}

$dbOrders   = $dbOrders ?? [];
$dbFoodsPos = $dbFoodsPos ?? [];

$pageTitle  = "Quản lý đơn hàng - Restaurant Management System";
$pageCss    = "public/asset/css/admin/orders.css";
$activePage = "orders";
include_once __DIR__ . '/../layouts/admin_header.php';
?>
            <!-- PAGE TITLE BAR -->
            <div class="page-title">
                <div>
                    <h1 class="page-title__heading">
                        <i class="bx bx-receipt"></i> Quản lý đơn hàng
                    </h1>
                    <p class="page-title__desc">
                        Theo dõi danh sách hóa đơn, gọi món tại bàn, mang về và trạng thái thanh toán thời gian
                        thực.
                    </p>
                </div>
                <div class="page-title__actions">
                    <button class="btn btn--secondary" type="button" id="btnExportData">
                        <i class="bx bx-export"></i> Xuất dữ liệu
                    </button>
                    <button class="btn btn--primary" type="button" id="btnOpenCreateOrder">
                        <i class="bx bx-plus-circle"></i> Tạo đơn hàng mới
                    </button>
                </div>
            </div>

            <!-- STAT CARDS (Theo phong cách overview) -->
            <div class="stat-cards">
                <!-- Doanh thu -->
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--primary">
                            <i class="bx bx-wallet-alt"></i>
                        </div>
                        <span class="stat-card__badge stat-card__badge--up" id="statRevenueBadge">+14.8%</span>
                    </div>
                    <p class="stat-card__label" id="statRevenueLabel">Doanh thu hôm nay</p>
                    <p class="stat-card__value" id="statTodayRevenue">18.450.000đ</p>
                </div>

                <!-- Tổng đơn -->
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--blue">
                            <i class="bx bx-receipt"></i>
                        </div>
                        <span class="stat-card__badge stat-card__badge--up" id="statOrdersBadge">+6 đơn</span>
                    </div>
                    <p class="stat-card__label" id="statOrdersLabel">Tổng đơn trong ngày</p>
                    <p class="stat-card__value" id="statTotalOrders">8</p>
                </div>

                <!-- Đang phục vụ -->
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--orange">
                            <i class="bx bx-dish"></i>
                        </div>
                        <span class="stat-card__badge stat-card__badge--neutral">Bàn bận</span>
                    </div>
                    <p class="stat-card__label">Đang phục vụ / Bếp</p>
                    <p class="stat-card__value" id="statServingOrders">3</p>
                </div>

                <!-- Chờ thanh toán -->
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--orange">
                            <i class="bx bx-time"></i>
                        </div>
                        <span class="stat-card__badge stat-card__badge--down">Cần thu</span>
                    </div>
                    <p class="stat-card__label">Chờ thanh toán</p>
                    <p class="stat-card__value" id="statPendingPayOrders">1</p>
                </div>

                <!-- Đã hoàn thành -->
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--green">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <span class="stat-card__badge stat-card__badge--up">Thành công</span>
                    </div>
                    <p class="stat-card__label">Đã thanh toán xong</p>
                    <p class="stat-card__value" id="statCompletedOrders">3</p>
                </div>
            </div>

            <!-- ORDERS DATA CARD -->
            <div class="orders-card">
                <!-- TABS LỌC TRẠNG THÁI -->
                <div class="orders-tabs" id="orderStatusTabs">
                    <div class="orders-tab active" data-status="all">
                        <i class="bx bx-list-ul"></i> Tất cả đơn
                        <span class="orders-tab__count" id="countAll">8</span>
                    </div>
                    <div class="orders-tab" data-status="cooking">
                        <i class="bx bx-bowl-hot"></i> Đang chế biến
                        <span class="orders-tab__count" id="countCooking">1</span>
                    </div>
                    <div class="orders-tab" data-status="serving">
                        <i class="bx bx-restaurant"></i> Đang phục vụ
                        <span class="orders-tab__count" id="countServing">2</span>
                    </div>
                    <div class="orders-tab" data-status="pending_pay">
                        <i class="bx bx-time-five"></i> Chờ thanh toán
                        <span class="orders-tab__count" id="countPendingPay">1</span>
                    </div>
                    <div class="orders-tab" data-status="paid">
                        <i class="bx bx-check-double"></i> Đã thanh toán
                        <span class="orders-tab__count" id="countPaid">3</span>
                    </div>
                    <div class="orders-tab" data-status="cancelled">
                        <i class="bx bx-x-circle"></i> Đã hủy
                        <span class="orders-tab__count" id="countCancelled">1</span>
                    </div>
                </div>

                <!-- TOOLBAR LỌC & TÌM KIẾM -->
                <div class="orders-toolbar">
                    <div class="orders-toolbar__left">
                        <!-- Ô tìm kiếm đơn -->
                        <div class="toolbar-search">
                            <i class="bx bx-search"></i>
                            <input type="text" id="orderSearchInput" placeholder="Tìm mã đơn, tên khách, số bàn..." />
                        </div>

                        <!-- Lọc loại đơn -->
                        <select class="toolbar-select" id="filterOrderType">
                            <option value="all">Tất cả hình thức</option>
                            <option value="dinein">Ăn tại bàn</option>
                            <option value="takeaway">Mang về</option>
                            <option value="delivery">Giao hàng tận nơi</option>
                        </select>

                        <!-- Lọc phương thức thanh toán -->
                        <select class="toolbar-select" id="filterPayment">
                            <option value="all">Tất cả thanh toán</option>
                            <option value="cash">Tiền mặt</option>
                            <option value="transfer">Chuyển khoản QR</option>
                            <option value="card">Quẹt thẻ (POS)</option>
                        </select>

                        <!-- Lọc thời gian: Ngày / Tuần / Tháng -->
                        <select class="toolbar-select" id="filterDate">
                            <option value="today">Hôm nay (8 đơn)</option>
                            <option value="yesterday">Hôm qua (5 đơn)</option>
                            <option value="week">Tuần này (18 đơn)</option>
                            <option value="month">Tháng này (24 đơn)</option>
                        </select>
                    </div>

                    <div class="orders-toolbar__right">
                        <button class="btn btn--secondary btn--sm" id="btnRefreshList" type="button"
                            title="Làm mới bộ lọc và khôi phục trạng thái mặc định">
                            <i class="bx bx-refresh"></i> Làm mới trạng thái
                        </button>
                    </div>
                </div>

                <!-- ORDERS TABLE -->
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="checkAllOrders" />
                                </th>
                                <th>MÃ ĐƠN</th>
                                <th>BÀN / HÌNH THỨC</th>
                                <th>KHÁCH HÀNG</th>
                                <th>MÓN ĂN GỌI</th>
                                <th>TỔNG TIỀN</th>
                                <th>THANH TOÁN</th>
                                <th>TRẠNG THÁI</th>
                                <th style="text-align: right;">THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <!-- Dữ liệu render động từ JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- TABLE FOOTER & PHÂN TRANG -->
                <div class="table-footer">
                    <div class="table-footer__info" id="tablePaginationInfo">
                        Hiển thị 1 - 8 trong tổng số 8 đơn hàng
                    </div>
                    <div class="pagination" id="ordersPaginationControls">
                        <button class="pagination__btn" id="prevPageBtn" disabled><i
                                class="bx bx-chevron-left"></i></button>
                        <button class="pagination__btn active">1</button>
                        <button class="pagination__btn">2</button>
                        <button class="pagination__btn">3</button>
                        <button class="pagination__btn" id="nextPageBtn" disabled><i
                                class="bx bx-chevron-right"></i></button>
                    </div>
                </div>
            </div>

    <!-- ==========================================================================
         MODAL 1: CHI TIẾT ĐƠN HÀNG & IN BILL
         ========================================================================== -->
    <div class="modal" id="orderDetailModal">
        <div class="modal__box modal__box--lg">
            <div class="modal__header">
                <div class="modal__title">
                    <i class="bx bx-receipt"></i>
                    <span>Chi tiết đơn hàng <strong id="modalOrderCode">#DH-1025</strong></span>
                </div>
                <button class="modal__close" id="btnCloseDetailModal" type="button">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <div class="modal__body">
                <!-- Thông tin tóm tắt -->
                <div class="order-summary-grid">
                    <div>
                        <p class="summary-item__label">Bàn / Địa điểm</p>
                        <p class="summary-item__value" id="detailModalTable">Bàn 04 (Tầng 1)</p>
                    </div>
                    <div>
                        <p class="summary-item__label">Khách hàng</p>
                        <p class="summary-item__value" id="detailModalCustomer">Nguyễn Hữu An (4 người)</p>
                    </div>
                    <div>
                        <p class="summary-item__label">Thời gian vào</p>
                        <p class="summary-item__value" id="detailModalTime">11:30 - 28/09/2026</p>
                    </div>
                    <div>
                        <p class="summary-item__label">Đổi trạng thái đơn</p>
                        <div id="detailModalStatusBadge">
                            <!-- Select đổi trạng thái trực tiếp trong modal -->
                        </div>
                    </div>
                </div>

                <!-- Quy trình phục vụ (Timeline tương tác) -->
                <div class="order-timeline" id="detailModalTimeline">
                    <!-- Render động & có thể nhấp để chuyển trạng thái -->
                </div>

                <!-- Danh sách món ăn trong đơn -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h3 style="font-size: 0.95rem; font-weight: 700;">Danh sách món đã gọi (Nhấp vào trạng thái món để đổi)</h3>
                    <button class="btn btn--secondary btn--sm" id="btnQuickAddDish" type="button">
                        <i class="bx bx-plus"></i> Gọi thêm món
                    </button>
                </div>

                <table class="detail-dishes-table">
                    <thead>
                        <tr>
                            <th>MÓN ĂN</th>
                            <th style="text-align: center;">SL</th>
                            <th style="text-align: right;">ĐƠN GIÁ</th>
                            <th style="text-align: right;">THÀNH TIỀN</th>
                            <th style="text-align: center;">BẾP / PHỤC VỤ</th>
                        </tr>
                    </thead>
                    <tbody id="detailModalDishesBody">
                        <!-- Render các món ăn -->
                    </tbody>
                </table>

                <!-- Tính toán hóa đơn -->
                <div class="bill-calculation">
                    <div class="bill-row">
                        <span>Tạm tính tiền món:</span>
                        <span id="detailSubtotal">1,250,000đ</span>
                    </div>
                    <div class="bill-row">
                        <span>Voucher giảm giá (<span id="detailVoucherCode">KM10</span>):</span>
                        <span style="color: var(--green);" id="detailDiscount">-125,000đ</span>
                    </div>
                    <div class="bill-row bill-row--total">
                        <span>TỔNG CỘNG THANH TOÁN:</span>
                        <span id="detailTotal">1,215,000đ</span>
                    </div>
                </div>
            </div>

            <div class="modal__footer">
                <button class="btn btn--ghost" id="btnCloseDetailModal2" type="button">Đóng</button>
                <button class="btn btn--secondary" id="btnPrintReceiptBtn" type="button">
                    <i class="bx bx-printer"></i> In hóa đơn
                </button>
                <button class="btn btn--primary" id="btnPayOrderAction" type="button">
                    <i class="bx bx-credit-card"></i> Thanh toán đơn này
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         MODAL 2: TẠO ĐƠN HÀNG MỚI (MINI POS NHÀ HÀNG)
         ========================================================================== -->
    <div class="modal" id="newOrderModal">
        <div class="modal__box modal__box--lg">
            <div class="modal__header">
                <div class="modal__title">
                    <i class="bx bx-plus-circle"></i>
                    <span>Tạo đơn hàng mới (POS Order)</span>
                </div>
                <button class="modal__close" id="btnCloseNewOrderModal" type="button">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <div class="modal__body">
                <div class="pos-layout">
                    <!-- Bên trái: Chọn món ăn -->
                    <div class="pos-menu">
                        <div class="form-group">
                            <label>Tìm kiếm món ăn:</label>
                            <input type="text" class="form-control" id="searchMenuInput"
                                placeholder="Nhập tên món, mã món..." />
                        </div>

                        <!-- Danh mục món -->
                        <div class="pos-categories" id="posCategoryTabs">
                            <button type="button" class="pos-cat-btn active" data-cat="all">Tất cả</button>
                            <button type="button" class="pos-cat-btn" data-cat="beef">Thịt bò & Lẩu</button>
                            <button type="button" class="pos-cat-btn" data-cat="seafood">Hải sản</button>
                            <button type="button" class="pos-cat-btn" data-cat="appetizer">Khai vị</button>
                            <button type="button" class="pos-cat-btn" data-cat="drink">Đồ uống</button>
                        </div>

                        <!-- Lưới món ăn -->
                        <div class="pos-dish-grid" id="posDishGrid">
                            <!-- Render danh sách món -->
                        </div>
                    </div>

                    <!-- Bên phải: Thông tin đơn & Món đã chọn -->
                    <div class="pos-order-panel">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label>Hình thức gọi món <span>*</span></label>
                                <select class="form-control" id="posOrderType">
                                    <option value="dinein">Ăn tại bàn</option>
                                    <option value="takeaway">Mang về</option>
                                    <option value="delivery">Giao hàng</option>
                                </select>
                            </div>
                            <div class="form-group" id="posTableSelectGroup">
                                <label>Chọn bàn <span>*</span></label>
                                <select class="form-control" id="posTableSelect">
                                    <option value="Bàn 01">Bàn 01 (Tầng 1)</option>
                                    <option value="Bàn 02">Bàn 02 (Tầng 1)</option>
                                    <option value="Bàn 03">Bàn 03 (Tầng 1)</option>
                                    <option value="Bàn 06">Bàn 06 (Tầng 2)</option>
                                    <option value="Bàn 07">Bàn 07 (Tầng 2)</option>
                                    <option value="VIP 01">Bàn VIP 01</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label>Tên khách hàng</label>
                                <input type="text" class="form-control" id="posCustomerName"
                                    placeholder="Khách vãng lai..." value="Khách lẻ" />
                            </div>
                            <div class="form-group">
                                <label>Số lượng khách</label>
                                <input type="number" class="form-control" id="posCustomerGuests" value="2" min="1" />
                            </div>
                        </div>

                        <!-- Danh sách món đã chọn -->
                        <div class="form-group">
                            <label>Món đã chọn (<span id="posSelectedCount">0</span>):</label>
                            <div class="pos-selected-dishes" id="posSelectedDishes">
                                <p style="font-size: 0.8rem; color: var(--text3); text-align: center; margin: auto;">
                                    Chưa có món nào được chọn. Nhấp vào món bên trái để thêm!
                                </p>
                            </div>
                        </div>

                        <!-- Voucher & Khuyến mãi -->
                        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label>Voucher khuyến mãi</label>
                                <select class="form-control" id="posVoucherSelect">
                                    <option value="0">Không áp dụng</option>
                                    <option value="10">GIAM10% - Giảm 10%</option>
                                    <option value="20">VIP20% - Tri ân VIP 20%</option>
                                    <option value="50000">TIENMAT50K - Giảm 50.000đ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Phương thức TT</label>
                                <select class="form-control" id="posPaymentMethod">
                                    <option value="cash">Tiền mặt</option>
                                    <option value="transfer">Chuyển khoản QR</option>
                                    <option value="card">Thẻ ngân hàng</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tóm tắt thanh toán -->
                        <div class="bill-calculation" style="padding: 10px 14px; margin-top: 5px;">
                            <div class="bill-row">
                                <span>Tạm tính:</span>
                                <span id="posSubtotal">0đ</span>
                            </div>
                            <div class="bill-row">
                                <span>Giảm giá:</span>
                                <span id="posDiscount" style="color: var(--green);">0đ</span>
                            </div>
                            <div class="bill-row bill-row--total"
                                style="font-size: 1rem; margin-top: 0; padding-top: 6px;">
                                <span>Tổng tiền:</span>
                                <span id="posFinalTotal">0đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal__footer">
                <button class="btn btn--ghost" id="btnCancelNewOrder" type="button">Hủy bỏ</button>
                <button class="btn btn--primary" id="btnSubmitNewOrder" type="button">
                    <i class="bx bx-check"></i> Xác nhận & Gửi xuống bếp
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         MODAL 3: IN HÓA ĐƠN BÁN HÀNG (BILL PRINT PREVIEW)
         ========================================================================== -->
    <div class="modal" id="printModal">
        <div class="modal__box modal__box--sm">
            <div class="modal__header">
                <div class="modal__title">
                    <i class="bx bx-printer"></i>
                    <span>Xem trước hóa đơn thanh toán</span>
                </div>
                <button class="modal__close" id="btnClosePrintModal" type="button">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="modal__body" style="background: #e5e5e5; padding: 20px;">
                <!-- Hóa đơn 80mm POS chuẩn -->
                <div class="invoice-print" id="printInvoiceArea">
                    <div class="invoice-print__header">
                        <h2>NHÀ HÀNG NHÓM 3</h2>
                        <p>Số 123 Đường Lẩu Nướng, Q. Ninh Kiều, TP. Cần Thơ</p>
                        <p>Hotline đặt bàn: 0987.654.321</p>
                        <p style="margin-top: 6px; font-weight: bold; font-size: 14px;">PHIẾU THANH TOÁN</p>
                    </div>

                    <div class="invoice-print__meta">
                        <div>Mã hóa đơn: <strong id="printInvoiceCode">#DH-1025</strong></div>
                        <div>Bàn: <strong id="printInvoiceTable">Bàn 04</strong> - Khách: <span
                                id="printInvoiceCust">Nguyễn Hữu An</span></div>
                        <div>Giờ vào: <span id="printInvoiceTimeIn">11:30 28/09/2026</span></div>
                        <div>Thu ngân: <span id="printInvoiceServer">Quản trị viên</span></div>
                    </div>

                    <table class="invoice-print__items">
                        <thead>
                            <tr>
                                <th>Tên món</th>
                                <th style="text-align: center;">SL</th>
                                <th style="text-align: right;">Đ.Giá</th>
                                <th style="text-align: right;">T.Tiền</th>
                            </tr>
                        </thead>
                        <tbody id="printInvoiceItemsBody">
                            <!-- Items -->
                        </tbody>
                    </table>

                    <div style="line-height: 1.6; font-size: 11px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Tạm tính:</span>
                            <span id="printInvoiceSubtotal">1,250,000đ</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Giảm giá (Voucher):</span>
                            <span id="printInvoiceDiscount">-125,000đ</span>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; border-top: 1px dashed #000; padding-top: 4px; margin-top: 4px;">
                            <span>TỔNG CỘNG:</span>
                            <span id="printInvoiceTotal">1,215,000đ</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Hình thức TT:</span>
                            <span id="printInvoicePaymentMethod">Chuyển khoản QR</span>
                        </div>
                    </div>

                    <div class="invoice-print__footer">
                        <p style="font-weight: bold; margin-bottom: 4px;">CẢM ƠN QUÝ KHÁCH & HẸN GẶP LẠI!</p>
                        <p>Wifi: NhaHangNhom3 - Pass: 88888888</p>
                        <p style="font-size: 9px; margin-top: 4px;">In lúc: 28/09/2026 12:45</p>
                    </div>
                </div>
            </div>
            <div class="modal__footer">
                <button class="btn btn--ghost" id="btnClosePrintModal2" type="button">Đóng</button>
                <button class="btn btn--primary" id="btnExecutePrint" type="button">
                    <i class="bx bx-printer"></i> Xác nhận In Bill
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         MODAL 4: HỦY ĐƠN HÀNG (CANCEL CONFIRMATION)
         ========================================================================== -->
    <div class="modal" id="cancelOrderModal">
        <div class="modal__box modal__box--sm">
            <div class="modal__header">
                <div class="modal__title" style="color: var(--red);">
                    <i class="bx bx-error-circle"></i>
                    <span>Xác nhận hủy đơn hàng</span>
                </div>
                <button class="modal__close" id="btnCloseCancelModal" type="button">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="modal__body">
                <p style="margin-bottom: 12px; font-size: 0.9rem;">
                    Bạn có chắc chắn muốn hủy đơn hàng <strong id="cancelModalOrderCode">#DH-1025</strong> không?
                </p>
                <div class="form-group">
                    <label>Lý do hủy đơn <span>*</span></label>
                    <select class="form-control" id="cancelOrderReason">
                        <option value="Khách đổi ý không dùng nữa">Khách đổi ý không dùng nữa</option>
                        <option value="Hết nguyên liệu / Món ăn">Hết nguyên liệu / Món ăn</option>
                        <option value="Khách đặt nhầm / Nhân viên nhập sai">Khách đặt nhầm / Nhân viên nhập sai
                        </option>
                        <option value="Sự cố bàn hoặc kỹ thuật">Sự cố bàn hoặc kỹ thuật</option>
                        <option value="Lý do khác">Lý do khác</option>
                    </select>
                </div>
            </div>
            <div class="modal__footer">
                <button class="btn btn--ghost" id="btnAbortCancel" type="button">Quay lại</button>
                <button class="btn btn--danger" id="btnConfirmCancelOrder" type="button">
                    <i class="bx bx-trash"></i> Xác nhận Hủy đơn
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- ==========================================================================
         JAVASCRIPT XỬ LÝ TOÀN BỘ CHỨC NĂNG QUẢN LÝ ĐƠN HÀNG
         ========================================================================== -->
    <script>
        // ---------------------------------------------------------
        // 1. DỮ LIỆU ĐƠN HÀNG TỪ DATABASE KẾT HỢP DỮ LIỆU MẪU THEO NGÀY, TUẦN, THÁNG
        // ---------------------------------------------------------
        const STATUS_MAP = {
            cooking:     { name: "Đang chế biến", badgeClass: "badge--cooking",     selectClass: "status-select--cooking" },
            serving:     { name: "Đang phục vụ",  badgeClass: "badge--serving",     selectClass: "status-select--serving" },
            pending_pay: { name: "Chờ thanh toán",badgeClass: "badge--pending-pay", selectClass: "status-select--pending_pay" },
            paid:        { name: "Đã thanh toán", badgeClass: "badge--paid",        selectClass: "status-select--paid" },
            cancelled:   { name: "Đã hủy",        badgeClass: "badge--cancelled",   selectClass: "status-select--cancelled" }
        };

        // Dữ liệu đơn hàng lấy trực tiếp từ bảng orders & order_items (restaurant_insert.sql)
        const initialOrdersData = <?= json_encode($dbOrders, JSON_UNESCAPED_UNICODE) ?>;
        let ordersData = JSON.parse(JSON.stringify(initialOrdersData));

        // ---------------------------------------------------------
        // 2. MENU MÓN ĂN DÀNH CHO MODAL TẠO ĐƠN HÀNG (Lấy trực tiếp từ bảng food trong Database)
        // ---------------------------------------------------------
        const menuDishes = <?= json_encode($dbFoodsPos, JSON_UNESCAPED_UNICODE) ?>;

        let selectedPosCart = [];
        let currentViewingOrderId = null;
        let currentStatusFilter = 'all';

        // ---------------------------------------------------------
        // 3. TIỆN ÍCH FORMAT TIỀN & THÔNG BÁO
        // ---------------------------------------------------------
        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount).replace('₫', 'đ');
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `toast toast--${type}`;
            let icon = 'bx-check-circle';
            if (type === 'danger' || type === 'error') icon = 'bx-x-circle';
            if (type === 'info') icon = 'bx-info-circle';

            toast.innerHTML = `<i class="bx ${icon}" style="font-size: 1.3rem;"></i> <span>${message}</span>`;
            container.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 50);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Đồng hồ thời gian thực
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById('clockTime');
            if (clockEl) clockEl.textContent = `${h}:${m}:${s}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ---------------------------------------------------------
        // 4. LỌC ĐƠN HÀNG THEO KỲ (NGÀY / HÔM QUA / TUẦN / THÁNG)
        // ---------------------------------------------------------
        function getOrdersByDatePeriod(dateFilterVal) {
            if (dateFilterVal === 'today') {
                return ordersData.filter(o => o.period === 'today');
            } else if (dateFilterVal === 'yesterday') {
                return ordersData.filter(o => o.period === 'yesterday');
            } else if (dateFilterVal === 'week') {
                // Tuần này gồm: Hôm nay + Hôm qua + Trong tuần
                return ordersData.filter(o => o.period === 'today' || o.period === 'yesterday' || o.period === 'week');
            } else {
                // Tháng này gồm toàn bộ đơn trong tháng
                return ordersData;
            }
        }

        // Cập nhật số lượng đơn hiển thị ngay trong các option của select #filterDate
        function updateDateSelectCounts() {
            const selectEl = document.getElementById('filterDate');
            if (!selectEl) return;
            const cToday     = getOrdersByDatePeriod('today').length;
            const cYesterday = getOrdersByDatePeriod('yesterday').length;
            const cWeek      = getOrdersByDatePeriod('week').length;
            const cMonth     = getOrdersByDatePeriod('month').length;

            selectEl.options[0].textContent = `Hôm nay (${cToday} đơn)`;
            selectEl.options[1].textContent = `Hôm qua (${cYesterday} đơn)`;
            selectEl.options[2].textContent = `Tuần này (${cWeek} đơn)`;
            selectEl.options[3].textContent = `Tháng này (${cMonth} đơn)`;
        }

        // ---------------------------------------------------------
        // 5. CẬP NHẬT CÁC THẺ THỐNG KÊ (STAT CARDS) & SỐ ĐẾM TABS
        // ---------------------------------------------------------
        function updateStatCards() {
            updateDateSelectCounts();

            const datePeriod = document.getElementById('filterDate').value;
            const periodOrders = getOrdersByDatePeriod(datePeriod);

            let totalRevenue = 0;
            let totalOrders = periodOrders.length;
            let countServing = 0;
            let countCooking = 0;
            let countPendingPay = 0;
            let countPaid = 0;
            let countCancelled = 0;

            periodOrders.forEach(order => {
                if (order.status === 'paid') {
                    totalRevenue += order.total;
                    countPaid++;
                } else if (order.status === 'serving') {
                    countServing++;
                } else if (order.status === 'cooking') {
                    countCooking++;
                } else if (order.status === 'pending_pay') {
                    countPendingPay++;
                } else if (order.status === 'cancelled') {
                    countCancelled++;
                }
            });

            // Đổi nhãn tiêu đề thẻ theo Ngày / Tuần / Tháng
            const periodLabels = {
                today:     { rev: "Doanh thu hôm nay",   ord: "Tổng đơn trong ngày",  badgeRev: "+14.8%", badgeOrd: `+${totalOrders} đơn` },
                yesterday: { rev: "Doanh thu hôm qua",   ord: "Tổng đơn hôm qua",     badgeRev: "+8.2%",  badgeOrd: `${totalOrders} đơn` },
                week:      { rev: "Doanh thu tuần này",  ord: "Tổng đơn trong tuần",  badgeRev: "+18.4%", badgeOrd: `${totalOrders} đơn` },
                month:     { rev: "Doanh thu tháng này", ord: "Tổng đơn trong tháng", badgeRev: "+22.6%", badgeOrd: `${totalOrders} đơn` }
            };
            const lbl = periodLabels[datePeriod] || periodLabels.today;

            document.getElementById('statRevenueLabel').textContent = lbl.rev;
            document.getElementById('statOrdersLabel').textContent  = lbl.ord;
            document.getElementById('statRevenueBadge').textContent = lbl.badgeRev;
            document.getElementById('statOrdersBadge').textContent  = lbl.badgeOrd;

            document.getElementById('statTodayRevenue').textContent     = formatVND(totalRevenue);
            document.getElementById('statTotalOrders').textContent      = totalOrders;
            document.getElementById('statServingOrders').textContent    = countServing + countCooking;
            document.getElementById('statPendingPayOrders').textContent = countPendingPay;
            document.getElementById('statCompletedOrders').textContent  = countPaid;

            // Cập nhật số đếm trên 6 Tabs trạng thái theo đúng kỳ Ngày / Tuần / Tháng
            document.getElementById('countAll').textContent        = totalOrders;
            document.getElementById('countCooking').textContent    = countCooking;
            document.getElementById('countServing').textContent    = countServing;
            document.getElementById('countPendingPay').textContent = countPendingPay;
            document.getElementById('countPaid').textContent       = countPaid;
            document.getElementById('countCancelled').textContent  = countCancelled;
        }

        // ---------------------------------------------------------
        // 6. THAO TÁC ĐỔI TRẠNG THÁI ĐƠN HÀNG & ĐỒNG BỘ DATABASE
        // ---------------------------------------------------------
        function changeOrderStatus(orderId, newStatus) {
            const order = ordersData.find(o => o.id === orderId);
            if (!order || !STATUS_MAP[newStatus]) return;

            order.status = newStatus;
            order.statusName = STATUS_MAP[newStatus].name;

            // Đồng bộ trạng thái món ăn bên trong đơn khi chuyển sang Đã thanh toán hoặc Đã hủy
            if (newStatus === 'paid' || newStatus === 'pending_pay' || newStatus === 'serving') {
                order.dishes.forEach(d => {
                    if (d.status === 'cancelled') d.status = 'done';
                    if (newStatus === 'paid' || newStatus === 'pending_pay') d.status = 'done';
                });
            } else if (newStatus === 'cancelled') {
                order.dishes.forEach(d => d.status = 'cancelled');
            }

            // Gửi yêu cầu AJAX POST lưu trạng thái mới xuống MySQL Database
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('orderId', orderId);
            formData.append('status', newStatus);
            fetch(window.location.href, { method: 'POST', body: formData }).catch(() => {});

            updateStatCards();
            renderOrdersTable();

            // Nếu đang mở modal chi tiết của chính đơn này thì cập nhật luôn modal
            if (currentViewingOrderId === orderId && document.getElementById('orderDetailModal').classList.contains('active')) {
                openOrderDetail(orderId);
            }

            const toastType = newStatus === 'cancelled' ? 'danger' : (newStatus === 'paid' ? 'success' : 'info');
            showToast(`Đơn ${order.id} đã chuyển sang trạng thái "${order.statusName}"!`, toastType);
        }

        // Chuyển nhanh sang bước trạng thái kế tiếp
        function advanceOrderNextStep(orderId) {
            const order = ordersData.find(o => o.id === orderId);
            if (!order) return;

            const nextStepMap = {
                cooking:     'serving',
                serving:     'pending_pay',
                pending_pay: 'paid',
                cancelled:   'cooking',
                paid:        'serving'
            };
            const nextStatus = nextStepMap[order.status] || 'serving';
            changeOrderStatus(orderId, nextStatus);
        }

        // Đổi trạng thái từng món ăn trong Modal chi tiết (Đang nấu <-> Đã lên món)
        function toggleDishStatus(orderId, dishIdx) {
            const order = ordersData.find(o => o.id === orderId);
            if (!order || !order.dishes[dishIdx]) return;

            const dish = order.dishes[dishIdx];
            dish.status = (dish.status === 'cooking') ? 'done' : 'cooking';

            openOrderDetail(orderId);
            renderOrdersTable();
            showToast(`Món "${dish.name}" đã cập nhật: ${dish.status === 'done' ? 'Đã lên món' : 'Đang nấu'}!`, 'info');
        }

        // ---------------------------------------------------------
        // 7. RENDER BẢNG ĐƠN HÀNG (Orders Table - Chuẩn 9 cột)
        // ---------------------------------------------------------
        function renderOrdersTable() {
            const tbody = document.getElementById('ordersTableBody');
            const searchKeyword = document.getElementById('orderSearchInput').value.toLowerCase().trim();
            const typeFilter = document.getElementById('filterOrderType').value;
            const paymentFilter = document.getElementById('filterPayment').value;
            const dateFilter = document.getElementById('filterDate').value;

            tbody.innerHTML = '';

            const periodOrders = getOrdersByDatePeriod(dateFilter);

            const filteredOrders = periodOrders.filter(order => {
                // Tab trạng thái
                if (currentStatusFilter !== 'all' && order.status !== currentStatusFilter) {
                    return false;
                }
                // Loại đơn
                if (typeFilter !== 'all' && order.type !== typeFilter) {
                    return false;
                }
                // Phương thức thanh toán
                if (paymentFilter !== 'all' && order.payment !== paymentFilter) {
                    return false;
                }
                // Từ khóa tìm kiếm
                if (searchKeyword) {
                    const matchId = order.id.toLowerCase().includes(searchKeyword);
                    const matchCust = order.customer.toLowerCase().includes(searchKeyword);
                    const matchPhone = order.phone.includes(searchKeyword);
                    const matchTable = order.table.toLowerCase().includes(searchKeyword);
                    if (!matchId && !matchCust && !matchPhone && !matchTable) return false;
                }
                return true;
            });

            if (filteredOrders.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--text3);">
                            <i class="bx bx-receipt" style="font-size: 3rem; display: block; margin-bottom: 10px; color: var(--border);"></i>
                            Không tìm thấy đơn hàng nào phù hợp với điều kiện lọc!
                        </td>
                    </tr>
                `;
                document.getElementById('tablePaginationInfo').textContent = 'Hiển thị 0 đơn hàng';
                return;
            }

            document.getElementById('tablePaginationInfo').textContent =
                `Hiển thị 1 - ${filteredOrders.length} trong tổng số ${periodOrders.length} đơn hàng`;

            filteredOrders.forEach(order => {
                const tr = document.createElement('tr');

                // Badge hình thức
                let typeBadgeClass = 'target-badge--dinein';
                let typeIcon = 'bx-restaurant';
                if (order.type === 'takeaway') {
                    typeBadgeClass = 'target-badge--takeaway';
                    typeIcon = 'bx-package';
                } else if (order.type === 'delivery') {
                    typeBadgeClass = 'target-badge--delivery';
                    typeIcon = 'bx-cycling';
                }

                // Badge trạng thái
                const statusCfg = STATUS_MAP[order.status] || STATUS_MAP.serving;

                // Tóm tắt món ăn
                let dishSummary = order.dishes.map(d => `${d.name} (x${d.qty})`).join(', ');
                if (dishSummary.length > 35) dishSummary = dishSummary.substring(0, 35) + '...';

                // Nút chuyển bước trạng thái nhanh
                let stepActionBtn = '';
                if (order.status === 'cooking') {
                    stepActionBtn = `
                        <button class="action-btn" title="Chuyển sang: Đang phục vụ" onclick="changeOrderStatus('${order.id}', 'serving')">
                            <i class="bx bx-dish" style="color: var(--primary-400);"></i>
                        </button>
                    `;
                } else if (order.status === 'serving') {
                    stepActionBtn = `
                        <button class="action-btn" title="Chuyển sang: Chờ thanh toán" onclick="changeOrderStatus('${order.id}', 'pending_pay')">
                            <i class="bx bx-time-five" style="color: var(--orange);"></i>
                        </button>
                    `;
                } else if (order.status === 'cancelled') {
                    stepActionBtn = `
                        <button class="action-btn" title="Khôi phục đơn hàng" onclick="changeOrderStatus('${order.id}', 'cooking')">
                            <i class="bx bx-revision" style="color: var(--blue);"></i>
                        </button>
                    `;
                }

                tr.innerHTML = `
                    <td style="text-align: center;" onclick="event.stopPropagation();">
                        <input type="checkbox" class="order-item-cb" value="${order.id}">
                    </td>
                    <td>
                        <div class="order-code">
                            <span class="order-code__id">${order.id}</span>
                            <span class="order-code__time"><i class="bx bx-time"></i> ${order.time}</span>
                        </div>
                    </td>
                    <td>
                        <div class="order-target">
                            <span class="target-badge ${typeBadgeClass}">
                                <i class="bx ${typeIcon}"></i> ${order.tableShort}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="customer-info">
                            <span class="customer-info__name">${order.customer}</span>
                            <span class="customer-info__sub"><i class="bx bx-phone"></i> ${order.phone}</span>
                        </div>
                    </td>
                    <td>
                        <div class="order-items-summary" title="${order.dishes.map(d => `${d.name} x${d.qty}`).join('\n')}">
                            <div class="order-items-summary__title">${dishSummary}</div>
                            <div class="order-items-summary__count">${order.dishes.length} món đã gọi</div>
                        </div>
                    </td>
                    <td>
                        <div class="order-amount">
                            <span class="order-amount__total">${formatVND(order.total)}</span>
                        </div>
                    </td>
                    <td>
                        <span class="order-amount__payment" style="font-size: 0.82rem; color: var(--text2);">
                            <i class="bx bx-credit-card-front"></i> ${order.paymentName}
                        </span>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <select class="status-select ${statusCfg.selectClass}"
                            onchange="changeOrderStatus('${order.id}', this.value)"
                            title="Nhấp để đổi trạng thái đơn hàng">
                            <option value="cooking"     ${order.status === 'cooking' ? 'selected' : ''}>Đang chế biến</option>
                            <option value="serving"     ${order.status === 'serving' ? 'selected' : ''}>Đang phục vụ</option>
                            <option value="pending_pay" ${order.status === 'pending_pay' ? 'selected' : ''}>Chờ thanh toán</option>
                            <option value="paid"        ${order.status === 'paid' ? 'selected' : ''}>Đã thanh toán</option>
                            <option value="cancelled"   ${order.status === 'cancelled' ? 'selected' : ''}>Đã hủy</option>
                        </select>
                    </td>
                    <td style="text-align: right;" onclick="event.stopPropagation();">
                        <div class="action-btns" style="justify-content: flex-end;">
                            <button class="action-btn" title="Xem chi tiết đơn" onclick="openOrderDetail('${order.id}')">
                                <i class="bx bx-show"></i>
                            </button>
                            ${stepActionBtn}
                            <button class="action-btn action-btn--print" title="In hóa đơn" onclick="openPrintInvoice('${order.id}')">
                                <i class="bx bx-printer"></i>
                            </button>
                            ${order.status !== 'paid' && order.status !== 'cancelled' ? `
                                <button class="action-btn action-btn--pay" title="Thanh toán ngay" onclick="quickPayOrder('${order.id}')">
                                    <i class="bx bx-check"></i>
                                </button>
                            ` : ''}
                            ${order.status !== 'cancelled' && order.status !== 'paid' ? `
                                <button class="action-btn action-btn--cancel" title="Hủy đơn" onclick="openCancelModal('${order.id}')">
                                    <i class="bx bx-x"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                `;

                // Bấm vào dòng để xem chi tiết
                tr.addEventListener('click', () => openOrderDetail(order.id));
                tbody.appendChild(tr);
            });
        }

        // ---------------------------------------------------------
        // 8. XỬ LÝ MODAL CHI TIẾT ĐƠN HÀNG (Order Detail)
        // ---------------------------------------------------------
        function openOrderDetail(orderId) {
            const order = ordersData.find(o => o.id === orderId);
            if (!order) return;
            currentViewingOrderId = orderId;

            document.getElementById('modalOrderCode').textContent = order.id;
            document.getElementById('detailModalTable').textContent = `${order.table} (${order.typeName})`;
            document.getElementById('detailModalCustomer').textContent = `${order.customer} (${order.guests} khách)`;
            document.getElementById('detailModalTime').textContent = order.time;

            const statusCfg = STATUS_MAP[order.status] || STATUS_MAP.serving;

            // Select đổi trạng thái trực tiếp ngay trong Modal
            document.getElementById('detailModalStatusBadge').innerHTML = `
                <select class="status-select ${statusCfg.selectClass}"
                    onchange="changeOrderStatus('${order.id}', this.value)">
                    <option value="cooking"     ${order.status === 'cooking' ? 'selected' : ''}>Đang chế biến</option>
                    <option value="serving"     ${order.status === 'serving' ? 'selected' : ''}>Đang phục vụ</option>
                    <option value="pending_pay" ${order.status === 'pending_pay' ? 'selected' : ''}>Chờ thanh toán</option>
                    <option value="paid"        ${order.status === 'paid' ? 'selected' : ''}>Đã thanh toán</option>
                    <option value="cancelled"   ${order.status === 'cancelled' ? 'selected' : ''}>Đã hủy</option>
                </select>
            `;

            // Render Timeline tương tác (nhấp vào bước nào sẽ chuyển đơn sang trạng thái đó)
            const timelineEl = document.getElementById('detailModalTimeline');
            if (order.status === 'cancelled') {
                timelineEl.innerHTML = `
                    <div class="timeline-step completed" style="cursor:pointer;" onclick="changeOrderStatus('${order.id}', 'cooking')" title="Nhấp để khôi phục sang Đang chế biến">
                        <div class="timeline-step__icon"><i class="bx bx-revision"></i></div>
                        <span class="timeline-step__text">Khôi phục đơn</span>
                    </div>
                    <div class="timeline-step" style="color: var(--red);">
                        <div class="timeline-step__icon" style="background: var(--red-bg); border-color: var(--red); color: var(--red);"><i class="bx bx-x"></i></div>
                        <span class="timeline-step__text" style="color: var(--red);">Đã hủy đơn</span>
                    </div>
                `;
            } else {
                const stepOrder = ['cooking', 'serving', 'pending_pay', 'paid'];
                const curIdx = stepOrder.indexOf(order.status);

                const getStepClass = (idx) => {
                    if (order.status === 'paid') return 'completed';
                    if (idx < curIdx) return 'completed';
                    if (idx === curIdx) return 'active';
                    return '';
                };

                timelineEl.innerHTML = `
                    <div class="timeline-step completed">
                        <div class="timeline-step__icon"><i class="bx bx-check"></i></div>
                        <span class="timeline-step__text">Tạo đơn</span>
                        <span class="timeline-step__time">${order.time.split(' ')[0]}</span>
                    </div>
                    <div class="timeline-step ${getStepClass(0)}" style="cursor:pointer;" onclick="changeOrderStatus('${order.id}', 'cooking')" title="Chuyển sang: Đang chế biến">
                        <div class="timeline-step__icon"><i class="bx bx-bowl-hot"></i></div>
                        <span class="timeline-step__text">Bếp chế biến</span>
                    </div>
                    <div class="timeline-step ${getStepClass(1)}" style="cursor:pointer;" onclick="changeOrderStatus('${order.id}', 'serving')" title="Chuyển sang: Đang phục vụ">
                        <div class="timeline-step__icon"><i class="bx bx-restaurant"></i></div>
                        <span class="timeline-step__text">Đang phục vụ</span>
                    </div>
                    <div class="timeline-step ${getStepClass(2)}" style="cursor:pointer;" onclick="changeOrderStatus('${order.id}', 'pending_pay')" title="Chuyển sang: Chờ thanh toán">
                        <div class="timeline-step__icon"><i class="bx bx-time-five"></i></div>
                        <span class="timeline-step__text">Chờ thanh toán</span>
                    </div>
                    <div class="timeline-step ${getStepClass(3)}" style="cursor:pointer;" onclick="changeOrderStatus('${order.id}', 'paid')" title="Chuyển sang: Đã thanh toán">
                        <div class="timeline-step__icon"><i class="bx bx-check-double"></i></div>
                        <span class="timeline-step__text">Đã thanh toán</span>
                    </div>
                `;
            }

            // Render danh sách món (bấm vào trạng thái món để đổi Đang nấu <-> Đã lên món)
            const dishesTbody = document.getElementById('detailModalDishesBody');
            dishesTbody.innerHTML = '';
            order.dishes.forEach((dish, idx) => {
                const tr = document.createElement('tr');
                let statusText = `<button type="button" onclick="toggleDishStatus('${order.id}', ${idx})" style="color: var(--green); font-size: 0.8rem; font-weight: 600; cursor: pointer;" title="Nhấp để đổi trạng thái món"><i class="bx bx-check"></i> Đã lên món</button>`;
                if (dish.status === 'cooking') {
                    statusText = `<button type="button" onclick="toggleDishStatus('${order.id}', ${idx})" style="color: var(--orange); font-size: 0.8rem; font-weight: 600; cursor: pointer;" title="Nhấp để đánh dấu Đã lên món"><i class="bx bx-loader-alt bx-spin"></i> Đang nấu</button>`;
                } else if (dish.status === 'cancelled') {
                    statusText = `<span style="color: var(--red); font-size: 0.8rem; font-weight: 600;"><i class="bx bx-x"></i> Đã hủy</span>`;
                }
                tr.innerHTML = `
                    <td>
                        <div class="dish-cell">
                            <div class="dish-cell__img"><i class="bx bx-dish"></i></div>
                            <div>
                                <p class="dish-cell__name">${dish.name}</p>
                                ${dish.note ? `<p class="dish-cell__note"><i class="bx bx-note"></i> ${dish.note}</p>` : ''}
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center; font-weight: 700;">x${dish.qty}</td>
                    <td style="text-align: right;">${formatVND(dish.price)}</td>
                    <td style="text-align: right; font-weight: 700; color: var(--primary-500);">${formatVND(dish.price * dish.qty)}</td>
                    <td style="text-align: center;">${statusText}</td>
                `;
                dishesTbody.appendChild(tr);
            });

            // Tiền nong
            document.getElementById('detailSubtotal').textContent = formatVND(order.subtotal);
            document.getElementById('detailVoucherCode').textContent = order.voucherCode || 'Không có';
            document.getElementById('detailDiscount').textContent = order.discount > 0 ? `-${formatVND(order.discount)}` : '0đ';
            document.getElementById('detailTotal').textContent = formatVND(order.total);

            // Nút thanh toán trong modal
            const payBtn = document.getElementById('btnPayOrderAction');
            if (order.status === 'paid' || order.status === 'cancelled') {
                payBtn.style.display = 'none';
            } else {
                payBtn.style.display = 'inline-flex';
            }

            document.getElementById('orderDetailModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('orderDetailModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('btnCloseDetailModal').addEventListener('click', closeDetailModal);
        document.getElementById('btnCloseDetailModal2').addEventListener('click', closeDetailModal);
        document.getElementById('orderDetailModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('orderDetailModal')) closeDetailModal();
        });

        // ---------------------------------------------------------
        // 9. XỬ LÝ MODAL TẠO ĐƠN HÀNG MỚI (Mini POS)
        // ---------------------------------------------------------
        const newOrderModal = document.getElementById('newOrderModal');
        const btnOpenCreateOrder = document.getElementById('btnOpenCreateOrder');
        const btnCloseNewOrderModal = document.getElementById('btnCloseNewOrderModal');
        const btnCancelNewOrder = document.getElementById('btnCancelNewOrder');

        function openNewOrderModal() {
            selectedPosCart = [];
            renderPosDishes('all');
            renderPosCart();
            newOrderModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeNewOrderModal() {
            newOrderModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        btnOpenCreateOrder.addEventListener('click', openNewOrderModal);
        btnCloseNewOrderModal.addEventListener('click', closeNewOrderModal);
        btnCancelNewOrder.addEventListener('click', closeNewOrderModal);
        newOrderModal.addEventListener('click', (e) => {
            if (e.target === newOrderModal) closeNewOrderModal();
        });

        // Render món trong POS
        function renderPosDishes(category = 'all', searchKeyword = '') {
            const grid = document.getElementById('posDishGrid');
            grid.innerHTML = '';

            const list = menuDishes.filter(item => {
                if (category !== 'all' && item.cat !== category) return false;
                if (searchKeyword && !item.name.toLowerCase().includes(searchKeyword.toLowerCase())) return false;
                return true;
            });

            list.forEach(dish => {
                const card = document.createElement('div');
                card.className = 'pos-dish-card';
                card.innerHTML = `
                    <div>
                        <p class="pos-dish-card__title">${dish.name}</p>
                        <span style="font-size: 0.73rem; color: var(--text3);">${dish.unit}</span>
                    </div>
                    <div class="pos-dish-card__bottom">
                        <span class="pos-dish-card__price">${formatVND(dish.price)}</span>
                        <div class="pos-dish-card__add"><i class="bx bx-plus"></i></div>
                    </div>
                `;
                card.addEventListener('click', () => addDishToPosCart(dish));
                grid.appendChild(card);
            });
        }

        // Lọc theo tabs category
        const catButtons = document.querySelectorAll('#posCategoryTabs .pos-cat-btn');
        catButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                catButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                renderPosDishes(this.dataset.cat, document.getElementById('searchMenuInput').value);
            });
        });

        // Tìm kiếm món trong POS
        document.getElementById('searchMenuInput').addEventListener('input', function () {
            const activeCat = document.querySelector('#posCategoryTabs .pos-cat-btn.active').dataset.cat;
            renderPosDishes(activeCat, this.value);
        });

        // Thêm món vào giỏ POS
        function addDishToPosCart(dish) {
            const found = selectedPosCart.find(item => item.id === dish.id);
            if (found) {
                found.qty += 1;
            } else {
                selectedPosCart.push({
                    id: dish.id,
                    name: dish.name,
                    price: dish.price,
                    qty: 1,
                    note: ""
                });
            }
            renderPosCart();
        }

        // Tăng giảm số lượng giỏ POS
        function updatePosCartQty(dishId, delta) {
            const found = selectedPosCart.find(item => item.id === dishId);
            if (!found) return;
            found.qty += delta;
            if (found.qty <= 0) {
                selectedPosCart = selectedPosCart.filter(item => item.id !== dishId);
            }
            renderPosCart();
        }

        // Render giỏ POS & Tính tiền tự động
        function renderPosCart() {
            const container = document.getElementById('posSelectedDishes');
            document.getElementById('posSelectedCount').textContent = selectedPosCart.reduce((sum, i) => sum + i.qty, 0);

            if (selectedPosCart.length === 0) {
                container.innerHTML = `
                    <p style="font-size: 0.8rem; color: var(--text3); text-align: center; margin: auto;">
                        Chưa có món nào được chọn. Nhấp vào món bên trái để thêm!
                    </p>
                `;
                document.getElementById('posSubtotal').textContent = '0đ';
                document.getElementById('posDiscount').textContent = '0đ';
                document.getElementById('posFinalTotal').textContent = '0đ';
                return;
            }

            container.innerHTML = '';
            let subtotal = 0;

            selectedPosCart.forEach(item => {
                subtotal += item.price * item.qty;
                const row = document.createElement('div');
                row.className = 'pos-selected-item';
                row.innerHTML = `
                    <div>
                        <p style="font-weight: 600;">${item.name}</p>
                        <span style="color: var(--primary-400);">${formatVND(item.price)}</span>
                    </div>
                    <div class="qty-control">
                        <button type="button" class="qty-btn" onclick="updatePosCartQty(${item.id}, -1)">-</button>
                        <span style="font-weight: 700; min-width: 16px; text-align: center;">${item.qty}</span>
                        <button type="button" class="qty-btn" onclick="updatePosCartQty(${item.id}, 1)">+</button>
                    </div>
                `;
                container.appendChild(row);
            });

            // Tính voucher
            const voucherVal = document.getElementById('posVoucherSelect').value;
            let discount = 0;
            if (voucherVal === '10') discount = subtotal * 0.1;
            else if (voucherVal === '20') discount = subtotal * 0.2;
            else if (voucherVal === '50000') discount = 50000;

            if (discount > subtotal) discount = subtotal;

            const finalTotal = subtotal - discount;

            document.getElementById('posSubtotal').textContent = formatVND(subtotal);
            document.getElementById('posDiscount').textContent = discount > 0 ? `-${formatVND(discount)}` : '0đ';
            document.getElementById('posFinalTotal').textContent = formatVND(finalTotal);
        }

        document.getElementById('posVoucherSelect').addEventListener('change', renderPosCart);

        // Đổi loại đơn (Tại bàn / Mang về / Giao hàng) trong POS
        document.getElementById('posOrderType').addEventListener('change', function () {
            const tableGroup = document.getElementById('posTableSelectGroup');
            if (this.value === 'takeaway' || this.value === 'delivery') {
                tableGroup.style.display = 'none';
            } else {
                tableGroup.style.display = 'flex';
            }
        });

        // Bấm Tạo đơn hàng mới (Gửi dữ liệu xuống OrderModel để INSERT vào Database)
        document.getElementById('btnSubmitNewOrder').addEventListener('click', function () {
            if (selectedPosCart.length === 0) {
                showToast('Vui lòng chọn ít nhất 1 món ăn cho đơn hàng!', 'danger');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'create_order');
            formData.append('orderType', document.getElementById('posOrderType').value);
            formData.append('table', document.getElementById('posTableSelect').value);
            formData.append('customerName', document.getElementById('posCustomerName').value.trim() || 'Khách lẻ');
            formData.append('guests', document.getElementById('posCustomerGuests').value || 2);
            formData.append('paymentMethod', document.getElementById('posPaymentMethod').value);
            formData.append('voucherVal', document.getElementById('posVoucherSelect').value);
            formData.append('dishes', JSON.stringify(selectedPosCart));

            fetch(window.location.href, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data && data.order) {
                        ordersData.unshift(data.order);
                        updateStatCards();
                        renderOrdersTable();
                        closeNewOrderModal();
                        showToast(`Đã tạo thành công đơn hàng ${data.order.id} vào Database và gửi xuống bếp!`, 'success');
                    } else {
                        showToast((data && data.message) || 'Không thể lưu đơn hàng vào cơ sở dữ liệu!', 'danger');
                    }
                })
                .catch(() => {
                    showToast('Lỗi kết nối khi lưu đơn hàng vào cơ sở dữ liệu!', 'danger');
                });
        });

        // ---------------------------------------------------------
        // 10. XỬ LÝ THANH TOÁN ĐƠN HÀNG (Payment Execution)
        // ---------------------------------------------------------
        function quickPayOrder(orderId) {
            changeOrderStatus(orderId, 'paid');
        }

        document.getElementById('btnPayOrderAction').addEventListener('click', function () {
            if (currentViewingOrderId) {
                quickPayOrder(currentViewingOrderId);
                closeDetailModal();
            }
        });

        // ---------------------------------------------------------
        // 11. XỬ LÝ MODAL IN HÓA ĐƠN (Print Invoice)
        // ---------------------------------------------------------
        const printModal = document.getElementById('printModal');

        function openPrintInvoice(orderId) {
            const order = ordersData.find(o => o.id === orderId);
            if (!order) return;

            document.getElementById('printInvoiceCode').textContent = order.id;
            document.getElementById('printInvoiceTable').textContent = order.tableShort;
            document.getElementById('printInvoiceCust').textContent = order.customer;
            document.getElementById('printInvoiceTimeIn').textContent = order.time;
            document.getElementById('printInvoiceServer').textContent = order.server || 'Quản trị viên';
            document.getElementById('printInvoiceSubtotal').textContent = formatVND(order.subtotal);
            document.getElementById('printInvoiceDiscount').textContent = order.discount > 0 ? `-${formatVND(order.discount)}` : '0đ';
            document.getElementById('printInvoiceTotal').textContent = formatVND(order.total);
            document.getElementById('printInvoicePaymentMethod').textContent = order.paymentName;

            const tbody = document.getElementById('printInvoiceItemsBody');
            tbody.innerHTML = '';
            order.dishes.forEach(d => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${d.name}</td>
                    <td style="text-align: center;">${d.qty}</td>
                    <td style="text-align: right;">${formatVND(d.price)}</td>
                    <td style="text-align: right;">${formatVND(d.price * d.qty)}</td>
                `;
                tbody.appendChild(tr);
            });

            printModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closePrintModal() {
            printModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        document.getElementById('btnClosePrintModal').addEventListener('click', closePrintModal);
        document.getElementById('btnClosePrintModal2').addEventListener('click', closePrintModal);
        printModal.addEventListener('click', (e) => {
            if (e.target === printModal) closePrintModal();
        });

        document.getElementById('btnPrintReceiptBtn').addEventListener('click', function () {
            if (currentViewingOrderId) {
                closeDetailModal();
                openPrintInvoice(currentViewingOrderId);
            }
        });

        document.getElementById('btnExecutePrint').addEventListener('click', function () {
            window.print();
            showToast('Lệnh in hóa đơn đã được gửi tới máy in nhiệt!', 'info');
            closePrintModal();
        });

        // ---------------------------------------------------------
        // 12. XỬ LÝ HỦY ĐƠN HÀNG (Cancel Order)
        // ---------------------------------------------------------
        let cancelOrderIdTarget = null;
        const cancelModal = document.getElementById('cancelOrderModal');

        function openCancelModal(orderId) {
            cancelOrderIdTarget = orderId;
            document.getElementById('cancelModalOrderCode').textContent = orderId;
            cancelModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            cancelModal.classList.remove('active');
            document.body.style.overflow = '';
            cancelOrderIdTarget = null;
        }

        document.getElementById('btnCloseCancelModal').addEventListener('click', closeCancelModal);
        document.getElementById('btnAbortCancel').addEventListener('click', closeCancelModal);
        cancelModal.addEventListener('click', (e) => {
            if (e.target === cancelModal) closeCancelModal();
        });

        document.getElementById('btnConfirmCancelOrder').addEventListener('click', function () {
            if (!cancelOrderIdTarget) return;
            const order = ordersData.find(o => o.id === cancelOrderIdTarget);
            if (order) {
                const reason = document.getElementById('cancelOrderReason').value;
                changeOrderStatus(order.id, 'cancelled');
                showToast(`Đã hủy đơn ${order.id} (Lý do: ${reason})`, 'danger');
            }
            closeCancelModal();
        });

        // ---------------------------------------------------------
        // 13. XỬ LÝ GỌI THÊM MÓN TRONG MODAL CHI TIẾT
        // ---------------------------------------------------------
        document.getElementById('btnQuickAddDish').addEventListener('click', function () {
            if (!currentViewingOrderId) return;
            const order = ordersData.find(o => o.id === currentViewingOrderId);
            if (!order) return;

            const randomDish = menuDishes[Math.floor(Math.random() * menuDishes.length)];
            order.dishes.push({
                name: randomDish.name,
                qty: 1,
                price: randomDish.price,
                note: "Gọi thêm lúc khách đang dùng",
                status: "cooking"
            });

            // Tái tính toán
            order.subtotal = order.dishes.reduce((s, i) => s + (i.price * i.qty), 0);
            if (order.voucherCode === 'GIAM10%') order.discount = order.subtotal * 0.1;
            order.total = Math.max(0, order.subtotal - order.discount);

            openOrderDetail(order.id);
            updateStatCards();
            renderOrdersTable();
            showToast(`Đã thêm món "${randomDish.name}" vào đơn ${order.id}!`, 'info');
        });

        // ---------------------------------------------------------
        // 14. SỰ KIỆN TÌM KIẾM, TAB TRẠNG THÁI, BỘ LỌC NGÀY/TUẦN/THÁNG & LÀM MỚI
        // ---------------------------------------------------------
        const tabItems = document.querySelectorAll('#orderStatusTabs .orders-tab');
        tabItems.forEach(tab => {
            tab.addEventListener('click', function () {
                tabItems.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentStatusFilter = this.dataset.status;
                renderOrdersTable();
            });
        });

        document.getElementById('orderSearchInput').addEventListener('input', renderOrdersTable);

        // Hỗ trợ ô tìm kiếm trên thanh Header chung
        const headerSearchInput = document.querySelector('.header__search input');
        if (headerSearchInput) {
            headerSearchInput.addEventListener('input', function () {
                document.getElementById('orderSearchInput').value = this.value;
                renderOrdersTable();
            });
        }

        document.getElementById('filterOrderType').addEventListener('change', renderOrdersTable);
        document.getElementById('filterPayment').addEventListener('change', renderOrdersTable);

        // Khi đổi Ngày / Tuần / Tháng: cập nhật cả 5 Thẻ thống kê, số đếm trên 6 Tabs và Bảng đơn hàng
        document.getElementById('filterDate').addEventListener('change', function () {
            updateStatCards();
            renderOrdersTable();
            const labelText = this.options[this.selectedIndex].text;
            showToast(`Đang hiển thị dữ liệu: ${labelText}`, 'info');
        });

        // Nút Làm mới trạng thái & bộ lọc
        document.getElementById('btnRefreshList').addEventListener('click', function () {
            // Khôi phục lại trạng thái gốc của các đơn hàng
            ordersData = JSON.parse(JSON.stringify(initialOrdersData));

            // Đặt lại toàn bộ bộ lọc
            document.getElementById('orderSearchInput').value = '';
            if (headerSearchInput) headerSearchInput.value = '';
            document.getElementById('filterOrderType').value = 'all';
            document.getElementById('filterPayment').value = 'all';
            document.getElementById('filterDate').value = 'today';
            currentStatusFilter = 'all';

            tabItems.forEach(t => t.classList.remove('active'));
            tabItems[0].classList.add('active');

            updateStatCards();
            renderOrdersTable();
            showToast('Đã làm mới bộ lọc và khôi phục lại trạng thái đơn hàng!', 'info');
        });

        // Check All
        document.getElementById('checkAllOrders').addEventListener('change', function () {
            const cbs = document.querySelectorAll('.order-item-cb');
            cbs.forEach(cb => cb.checked = this.checked);
        });

        // Xuất dữ liệu Excel / CSV
        document.getElementById('btnExportData').addEventListener('click', function () {
            const currentPeriodOrders = getOrdersByDatePeriod(document.getElementById('filterDate').value);
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
            csvContent += "Mã đơn,Bàn/Hình thức,Khách hàng,Số điện thoại,Tổng tiền,Thanh toán,Trạng thái,Thời gian\n";

            currentPeriodOrders.forEach(o => {
                csvContent += `"${o.id}","${o.tableShort}","${o.customer}","${o.phone}","${o.total}","${o.paymentName}","${o.statusName}","${o.time}"\n`;
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `Danh_sach_don_hang_${new Date().toISOString().slice(0, 10)}.csv`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            showToast('Đã xuất file dữ liệu đơn hàng thành công!', 'success');
        });

        // Khởi động render ban đầu
        updateStatCards();
        renderOrdersTable();
    </script>

    <!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
    </main>
  </div>

</body>

</html>
