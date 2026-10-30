<?php
$pageTitle = "Quản lý bàn - Nhóm 3";
$pageCss = "../../../public/asset/css/admin/tables.css";
$activePage = "tables";
include_once __DIR__ . '/../layouts/admin_header.php';
?>
<!-- PAGE TITLE -->
            <div class="page-title">
                <div>
                    <h1 class="page-title__heading">Quản lý bàn</h1>
                    <p class="page-title__desc">Quản lý sơ đồ bàn, trạng thái và xếp khách theo thời gian thực.</p>
                </div>
                <div class="page-title__actions">
                    <div class="view-toggle">
                        <button class="view-toggle__btn active" data-view="grid" title="Xem dạng lưới">
                            <i class="bx bx-grid-alt"></i>
                        </button>
                        <button class="view-toggle__btn" data-view="list" title="Xem dạng danh sách">
                            <i class="bx bx-list-ul"></i>
                        </button>
                    </div>
                    <button class="btn btn--primary" id="btnAddTable" type="button">
                        <i class="bx bx-plus"></i> Thêm bàn mới
                    </button>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div class="stat-cards">
                <div class="stat-card stat-card--total">
                    <div class="stat-card__icon"><i class="bx bx-table"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statTotal">25</p>
                        <p class="stat-card__label">Tổng số bàn</p>
                    </div>
                </div>
                <div class="stat-card stat-card--available">
                    <div class="stat-card__icon"><i class="bx bx-check-circle"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statAvailable">10</p>
                        <p class="stat-card__label">Bàn trống</p>
                    </div>
                </div>
                <div class="stat-card stat-card--occupied">
                    <div class="stat-card__icon"><i class="bx bx-user-check"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statOccupied">9</p>
                        <p class="stat-card__label">Đang phục vụ</p>
                    </div>
                </div>
                <div class="stat-card stat-card--reserved">
                    <div class="stat-card__icon"><i class="bx bx-calendar-check"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statReserved">4</p>
                        <p class="stat-card__label">Đã đặt trước</p>
                    </div>
                </div>
                <div class="stat-card stat-card--maintenance">
                    <div class="stat-card__icon"><i class="bx bx-wrench"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statMaintenance">2</p>
                        <p class="stat-card__label">Bảo trì</p>
                    </div>
                </div>
            </div>

            <!-- TOOLBAR -->
            <div class="toolbar">
                <div class="toolbar__left">
                    <div class="toolbar__filter-group">
                        <label class="toolbar__label">Khu vực:</label>
                        <select class="toolbar__select" id="filterZone">
                            <option value="">Tất cả</option>
                            <option value="tang1-trong">Tầng 1 - Trong nhà</option>
                            <option value="tang1-ngoai">Tầng 1 - Ngoài trời</option>
                            <option value="tang2-vip">Tầng 2 - VIP</option>
                            <option value="tang2-rieng">Tầng 2 - Phòng riêng</option>
                        </select>
                    </div>
                    <div class="toolbar__filter-group">
                        <label class="toolbar__label">Trạng thái:</label>
                        <select class="toolbar__select" id="filterStatus">
                            <option value="">Tất cả</option>
                            <option value="available">Trống</option>
                            <option value="occupied">Đang phục vụ</option>
                            <option value="reserved">Đã đặt trước</option>
                            <option value="maintenance">Bảo trì</option>
                        </select>
                    </div>
                    <div class="toolbar__filter-group">
                        <label class="toolbar__label">Sức chứa:</label>
                        <select class="toolbar__select" id="filterCapacity">
                            <option value="">Tất cả</option>
                            <option value="2">2 người</option>
                            <option value="4">4 người</option>
                            <option value="6">6 người</option>
                            <option value="8">8 người</option>
                            <option value="10">10+ người</option>
                        </select>
                    </div>
                </div>
                <div class="toolbar__right">
                    <!-- STATUS LEGEND -->
                    <div class="status-legend">
                        <span class="status-legend__item"><span class="status-dot status-dot--available"></span>
                            Trống</span>
                        <span class="status-legend__item"><span class="status-dot status-dot--occupied"></span> Đang
                            phục vụ</span>
                        <span class="status-legend__item"><span class="status-dot status-dot--reserved"></span> Đã
                            đặt</span>
                        <span class="status-legend__item"><span class="status-dot status-dot--maintenance"></span> Bảo
                            trì</span>
                    </div>
                </div>
            </div>

            <!-- ===========================
                 GRID VIEW - SƠ ĐỒ BÀN 
            ============================ -->
            <div class="tables-view tables-view--grid active" id="gridView">

                <!-- KHU VỰC: Tầng 1 - Trong nhà -->
                <div class="zone-section">
                    <div class="zone-section__header">
                        <div class="zone-section__title">
                            <i class="bx bx-building-house"></i>
                            <h2>Tầng 1 - Trong nhà</h2>
                            <span class="zone-section__count">8 bàn</span>
                        </div>
                    </div>
                    <div class="tables-grid">
                        <!-- Bàn 01 -->
                        <div class="table-card table-card--available" data-id="1" data-zone="tang1-trong"
                            data-status="available" data-capacity="4">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 01</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon">
                                <i class="bx bx-chair"></i>
                            </div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 4 người</span>
                                <span class="table-card__status-text">Trống</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--seat" title="Xếp khách"
                                    onclick="seatGuest(1)">
                                    <i class="bx bx-user-plus"></i> Xếp khách
                                </button>
                                <button class="table-card__btn table-card__btn--more" title="Thêm"
                                    onclick="toggleMenu(1)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                            </div>
                            <div class="table-card__menu" id="menu-1">
                                <button onclick="editTable(1)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button onclick="changeStatus(1, 'reserved')"><i class="bx bx-calendar-check"></i> Đặt
                                    trước</button>
                                <button onclick="changeStatus(1, 'maintenance')"><i class="bx bx-wrench"></i> Bảo
                                    trì</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(1)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                        <!-- Bàn 02 -->
                        <div class="table-card table-card--occupied" data-id="2" data-zone="tang1-trong"
                            data-status="occupied" data-capacity="4">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 02</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon">
                                <i class="bx bx-chair"></i>
                            </div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 4 người</span>
                                <span class="table-card__status-text">Đang phục vụ</span>
                            </div>
                            <div class="table-card__time">
                                <i class="bx bx-time-five"></i> 45 phút
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--bill" title="Thanh toán"
                                    onclick="billTable(2)">
                                    <i class="bx bx-receipt"></i> Thanh toán
                                </button>
                                <button class="table-card__btn table-card__btn--more" title="Thêm"
                                    onclick="toggleMenu(2)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                            </div>
                            <div class="table-card__menu" id="menu-2">
                                <button onclick="changeStatus(2, 'available')"><i class="bx bx-check-circle"></i> Trả
                                    bàn</button>
                                <button onclick="editTable(2)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <!-- Bàn 03 -->
                        <div class="table-card table-card--occupied" data-id="3" data-zone="tang1-trong"
                            data-status="occupied" data-capacity="2">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 03</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon">
                                <i class="bx bx-chair"></i>
                            </div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 2 người</span>
                                <span class="table-card__status-text">Đang phục vụ</span>
                            </div>
                            <div class="table-card__time">
                                <i class="bx bx-time-five"></i> 1h 20 phút
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--bill" title="Thanh toán"
                                    onclick="billTable(3)">
                                    <i class="bx bx-receipt"></i> Thanh toán
                                </button>
                                <button class="table-card__btn table-card__btn--more" title="Thêm"
                                    onclick="toggleMenu(3)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                            </div>
                            <div class="table-card__menu" id="menu-3">
                                <button onclick="changeStatus(3, 'available')"><i class="bx bx-check-circle"></i> Trả
                                    bàn</button>
                                <button onclick="editTable(3)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <!-- Bàn 04 -->
                        <div class="table-card table-card--available" data-id="4" data-zone="tang1-trong"
                            data-status="available" data-capacity="6">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 04</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon">
                                <i class="bx bx-chair"></i>
                            </div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 6 người</span>
                                <span class="table-card__status-text">Trống</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--seat" title="Xếp khách"
                                    onclick="seatGuest(4)">
                                    <i class="bx bx-user-plus"></i> Xếp khách
                                </button>
                                <button class="table-card__btn table-card__btn--more" title="Thêm"
                                    onclick="toggleMenu(4)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                            </div>
                            <div class="table-card__menu" id="menu-4">
                                <button onclick="editTable(4)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button onclick="changeStatus(4, 'reserved')"><i class="bx bx-calendar-check"></i> Đặt
                                    trước</button>
                                <button onclick="changeStatus(4, 'maintenance')"><i class="bx bx-wrench"></i> Bảo
                                    trì</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(4)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                        <!-- Bàn 05 -->
                        <div class="table-card table-card--reserved" data-id="5" data-zone="tang1-trong"
                            data-status="reserved" data-capacity="4">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 05</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon">
                                <i class="bx bx-chair"></i>
                            </div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 4 người</span>
                                <span class="table-card__status-text">Đã đặt trước</span>
                            </div>
                            <div class="table-card__reserved-info">
                                <i class="bx bx-calendar"></i> 19:00 - Nguyễn Văn A
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--checkin" title="Check-in"
                                    onclick="changeStatus(5, 'occupied')">
                                    <i class="bx bx-log-in-circle"></i> Check-in
                                </button>
                                <button class="table-card__btn table-card__btn--more" title="Thêm"
                                    onclick="toggleMenu(5)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                            </div>
                            <div class="table-card__menu" id="menu-5">
                                <button onclick="changeStatus(5, 'available')"><i class="bx bx-x-circle"></i> Hủy
                                    đặt</button>
                                <button onclick="editTable(5)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <!-- Bàn 06 -->
                        <div class="table-card table-card--available" data-id="6" data-zone="tang1-trong"
                            data-status="available" data-capacity="2">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 06</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon">
                                <i class="bx bx-chair"></i>
                            </div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 2 người</span>
                                <span class="table-card__status-text">Trống</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--seat" title="Xếp khách"
                                    onclick="seatGuest(6)">
                                    <i class="bx bx-user-plus"></i> Xếp khách
                                </button>
                                <button class="table-card__btn table-card__btn--more" title="Thêm"
                                    onclick="toggleMenu(6)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                            </div>
                            <div class="table-card__menu" id="menu-6">
                                <button onclick="editTable(6)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button onclick="changeStatus(6, 'reserved')"><i class="bx bx-calendar-check"></i> Đặt
                                    trước</button>
                                <button onclick="changeStatus(6, 'maintenance')"><i class="bx bx-wrench"></i> Bảo
                                    trì</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(6)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                        <!-- Bàn 07 -->
                        <div class="table-card table-card--occupied" data-id="7" data-zone="tang1-trong"
                            data-status="occupied" data-capacity="6">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 07</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon">
                                <i class="bx bx-chair"></i>
                            </div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 6 người</span>
                                <span class="table-card__status-text">Đang phục vụ</span>
                            </div>
                            <div class="table-card__time">
                                <i class="bx bx-time-five"></i> 30 phút
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--bill" title="Thanh toán"
                                    onclick="billTable(7)">
                                    <i class="bx bx-receipt"></i> Thanh toán
                                </button>
                                <button class="table-card__btn table-card__btn--more" title="Thêm"
                                    onclick="toggleMenu(7)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                            </div>
                            <div class="table-card__menu" id="menu-7">
                                <button onclick="changeStatus(7, 'available')"><i class="bx bx-check-circle"></i> Trả
                                    bàn</button>
                                <button onclick="editTable(7)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <!-- Bàn 08 -->
                        <div class="table-card table-card--maintenance" data-id="8" data-zone="tang1-trong"
                            data-status="maintenance" data-capacity="4">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 08</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon">
                                <i class="bx bx-chair"></i>
                            </div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 4 người</span>
                                <span class="table-card__status-text">Bảo trì</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--restore" title="Hoàn tất"
                                    onclick="changeStatus(8, 'available')">
                                    <i class="bx bx-check"></i> Hoàn tất
                                </button>
                                <button class="table-card__btn table-card__btn--more" title="Thêm"
                                    onclick="toggleMenu(8)">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                            </div>
                            <div class="table-card__menu" id="menu-8">
                                <button onclick="editTable(8)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(8)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHU VỰC: Tầng 1 - Ngoài trời -->
                <div class="zone-section">
                    <div class="zone-section__header">
                        <div class="zone-section__title">
                            <i class="bx bx-sun"></i>
                            <h2>Tầng 1 - Ngoài trời</h2>
                            <span class="zone-section__count">5 bàn</span>
                        </div>
                    </div>
                    <div class="tables-grid">
                        <div class="table-card table-card--available" data-id="9" data-zone="tang1-ngoai"
                            data-status="available" data-capacity="4">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 09</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-chair"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 4 người</span>
                                <span class="table-card__status-text">Trống</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--seat" onclick="seatGuest(9)"><i
                                        class="bx bx-user-plus"></i> Xếp khách</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(9)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-9">
                                <button onclick="editTable(9)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button onclick="changeStatus(9, 'reserved')"><i class="bx bx-calendar-check"></i> Đặt
                                    trước</button>
                                <button onclick="changeStatus(9, 'maintenance')"><i class="bx bx-wrench"></i> Bảo
                                    trì</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(9)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                        <div class="table-card table-card--occupied" data-id="10" data-zone="tang1-ngoai"
                            data-status="occupied" data-capacity="2">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 10</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-chair"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 2 người</span>
                                <span class="table-card__status-text">Đang phục vụ</span>
                            </div>
                            <div class="table-card__time"><i class="bx bx-time-five"></i> 15 phút</div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--bill" onclick="billTable(10)"><i
                                        class="bx bx-receipt"></i> Thanh toán</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(10)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-10">
                                <button onclick="changeStatus(10, 'available')"><i class="bx bx-check-circle"></i> Trả
                                    bàn</button>
                                <button onclick="editTable(10)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <div class="table-card table-card--available" data-id="11" data-zone="tang1-ngoai"
                            data-status="available" data-capacity="4">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 11</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-chair"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 4 người</span>
                                <span class="table-card__status-text">Trống</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--seat" onclick="seatGuest(11)"><i
                                        class="bx bx-user-plus"></i> Xếp khách</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(11)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-11">
                                <button onclick="editTable(11)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button onclick="changeStatus(11, 'reserved')"><i class="bx bx-calendar-check"></i> Đặt
                                    trước</button>
                                <button onclick="changeStatus(11, 'maintenance')"><i class="bx bx-wrench"></i> Bảo
                                    trì</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(11)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                        <div class="table-card table-card--reserved" data-id="12" data-zone="tang1-ngoai"
                            data-status="reserved" data-capacity="6">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 12</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-chair"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 6 người</span>
                                <span class="table-card__status-text">Đã đặt trước</span>
                            </div>
                            <div class="table-card__reserved-info"><i class="bx bx-calendar"></i> 20:00 - Trần Văn B
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--checkin"
                                    onclick="changeStatus(12, 'occupied')"><i class="bx bx-log-in-circle"></i>
                                    Check-in</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(12)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-12">
                                <button onclick="changeStatus(12, 'available')"><i class="bx bx-x-circle"></i> Hủy
                                    đặt</button>
                                <button onclick="editTable(12)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <div class="table-card table-card--occupied" data-id="13" data-zone="tang1-ngoai"
                            data-status="occupied" data-capacity="4">
                            <div class="table-card__header">
                                <span class="table-card__number">Bàn 13</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-chair"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 4 người</span>
                                <span class="table-card__status-text">Đang phục vụ</span>
                            </div>
                            <div class="table-card__time"><i class="bx bx-time-five"></i> 55 phút</div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--bill" onclick="billTable(13)"><i
                                        class="bx bx-receipt"></i> Thanh toán</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(13)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-13">
                                <button onclick="changeStatus(13, 'available')"><i class="bx bx-check-circle"></i> Trả
                                    bàn</button>
                                <button onclick="editTable(13)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHU VỰC: Tầng 2 - VIP -->
                <div class="zone-section">
                    <div class="zone-section__header">
                        <div class="zone-section__title">
                            <i class="bx bx-crown"></i>
                            <h2>Tầng 2 - VIP</h2>
                            <span class="zone-section__count">4 bàn</span>
                        </div>
                    </div>
                    <div class="tables-grid">
                        <div class="table-card table-card--available" data-id="14" data-zone="tang2-vip"
                            data-status="available" data-capacity="8">
                            <div class="table-card__header">
                                <span class="table-card__number">VIP 01</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-crown"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 8 người</span>
                                <span class="table-card__status-text">Trống</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--seat" onclick="seatGuest(14)"><i
                                        class="bx bx-user-plus"></i> Xếp khách</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(14)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-14">
                                <button onclick="editTable(14)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button onclick="changeStatus(14, 'reserved')"><i class="bx bx-calendar-check"></i> Đặt
                                    trước</button>
                                <button onclick="changeStatus(14, 'maintenance')"><i class="bx bx-wrench"></i> Bảo
                                    trì</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(14)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                        <div class="table-card table-card--occupied" data-id="15" data-zone="tang2-vip"
                            data-status="occupied" data-capacity="10">
                            <div class="table-card__header">
                                <span class="table-card__number">VIP 02</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-crown"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 10 người</span>
                                <span class="table-card__status-text">Đang phục vụ</span>
                            </div>
                            <div class="table-card__time"><i class="bx bx-time-five"></i> 1h 05 phút</div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--bill" onclick="billTable(15)"><i
                                        class="bx bx-receipt"></i> Thanh toán</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(15)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-15">
                                <button onclick="changeStatus(15, 'available')"><i class="bx bx-check-circle"></i> Trả
                                    bàn</button>
                                <button onclick="editTable(15)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <div class="table-card table-card--reserved" data-id="16" data-zone="tang2-vip"
                            data-status="reserved" data-capacity="8">
                            <div class="table-card__header">
                                <span class="table-card__number">VIP 03</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-crown"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 8 người</span>
                                <span class="table-card__status-text">Đã đặt trước</span>
                            </div>
                            <div class="table-card__reserved-info"><i class="bx bx-calendar"></i> 18:30 - Lê Thị C</div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--checkin"
                                    onclick="changeStatus(16, 'occupied')"><i class="bx bx-log-in-circle"></i>
                                    Check-in</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(16)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-16">
                                <button onclick="changeStatus(16, 'available')"><i class="bx bx-x-circle"></i> Hủy
                                    đặt</button>
                                <button onclick="editTable(16)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <div class="table-card table-card--available" data-id="17" data-zone="tang2-vip"
                            data-status="available" data-capacity="6">
                            <div class="table-card__header">
                                <span class="table-card__number">VIP 04</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-crown"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 6 người</span>
                                <span class="table-card__status-text">Trống</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--seat" onclick="seatGuest(17)"><i
                                        class="bx bx-user-plus"></i> Xếp khách</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(17)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-17">
                                <button onclick="editTable(17)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button onclick="changeStatus(17, 'reserved')"><i class="bx bx-calendar-check"></i> Đặt
                                    trước</button>
                                <button onclick="changeStatus(17, 'maintenance')"><i class="bx bx-wrench"></i> Bảo
                                    trì</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(17)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHU VỰC: Tầng 2 - Phòng riêng -->
                <div class="zone-section">
                    <div class="zone-section__header">
                        <div class="zone-section__title">
                            <i class="bx bx-door-open"></i>
                            <h2>Tầng 2 - Phòng riêng</h2>
                            <span class="zone-section__count">3 bàn</span>
                        </div>
                    </div>
                    <div class="tables-grid">
                        <div class="table-card table-card--occupied" data-id="18" data-zone="tang2-rieng"
                            data-status="occupied" data-capacity="12">
                            <div class="table-card__header">
                                <span class="table-card__number">P.Riêng 01</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-door-open"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 12 người</span>
                                <span class="table-card__status-text">Đang phục vụ</span>
                            </div>
                            <div class="table-card__time"><i class="bx bx-time-five"></i> 2h 10 phút</div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--bill" onclick="billTable(18)"><i
                                        class="bx bx-receipt"></i> Thanh toán</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(18)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-18">
                                <button onclick="changeStatus(18, 'available')"><i class="bx bx-check-circle"></i> Trả
                                    bàn</button>
                                <button onclick="editTable(18)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                            </div>
                        </div>
                        <div class="table-card table-card--available" data-id="19" data-zone="tang2-rieng"
                            data-status="available" data-capacity="10">
                            <div class="table-card__header">
                                <span class="table-card__number">P.Riêng 02</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-door-open"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 10 người</span>
                                <span class="table-card__status-text">Trống</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--seat" onclick="seatGuest(19)"><i
                                        class="bx bx-user-plus"></i> Xếp khách</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(19)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-19">
                                <button onclick="editTable(19)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button onclick="changeStatus(19, 'reserved')"><i class="bx bx-calendar-check"></i> Đặt
                                    trước</button>
                                <button onclick="changeStatus(19, 'maintenance')"><i class="bx bx-wrench"></i> Bảo
                                    trì</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(19)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                        <div class="table-card table-card--maintenance" data-id="20" data-zone="tang2-rieng"
                            data-status="maintenance" data-capacity="15">
                            <div class="table-card__header">
                                <span class="table-card__number">P.Riêng 03</span>
                                <div class="table-card__status-dot"></div>
                            </div>
                            <div class="table-card__icon"><i class="bx bx-door-open"></i></div>
                            <div class="table-card__info">
                                <span class="table-card__capacity"><i class="bx bx-user"></i> 15 người</span>
                                <span class="table-card__status-text">Bảo trì</span>
                            </div>
                            <div class="table-card__actions">
                                <button class="table-card__btn table-card__btn--restore"
                                    onclick="changeStatus(20, 'available')"><i class="bx bx-check"></i> Hoàn
                                    tất</button>
                                <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(20)"><i
                                        class="bx bx-dots-vertical-rounded"></i></button>
                            </div>
                            <div class="table-card__menu" id="menu-20">
                                <button onclick="editTable(20)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                <button class="table-card__menu-danger" onclick="deleteTable(20)"><i
                                        class="bx bx-trash"></i> Xóa bàn</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===========================
                 LIST VIEW - BẢNG DANH SÁCH 
            ============================ -->
            <div class="tables-view tables-view--list" id="listView">
                <div class="list-card">
                    <div class="table-wrapper">
                        <table class="tables-table">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="custom-checkbox">
                                            <input type="checkbox" id="checkAllTables">
                                            <span class="custom-checkbox__mark"></span>
                                        </label>
                                    </th>
                                    <th>SỐ BÀN</th>
                                    <th>KHU VỰC</th>
                                    <th>SỨC CHỨA</th>
                                    <th>TRẠNG THÁI</th>
                                    <th>THỜI GIAN SỬ DỤNG</th>
                                    <th>GHI CHÚ</th>
                                    <th>HÀNH ĐỘNG</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><label class="custom-checkbox"><input type="checkbox"><span
                                                class="custom-checkbox__mark"></span></label></td>
                                    <td><strong>Bàn 01</strong></td>
                                    <td><span class="zone-tag zone-tag--indoor">Tầng 1 - Trong nhà</span></td>
                                    <td><i class="bx bx-user"></i> 4 người</td>
                                    <td><span class="status-badge status-badge--available">Trống</span></td>
                                    <td>—</td>
                                    <td>—</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn action-btn--seat" title="Xếp khách"><i
                                                    class="bx bx-user-plus"></i></button>
                                            <button class="action-btn" title="Sửa"><i
                                                    class="bx bx-edit-alt"></i></button>
                                            <button class="action-btn action-btn--danger" title="Xóa"><i
                                                    class="bx bx-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="custom-checkbox"><input type="checkbox"><span
                                                class="custom-checkbox__mark"></span></label></td>
                                    <td><strong>Bàn 02</strong></td>
                                    <td><span class="zone-tag zone-tag--indoor">Tầng 1 - Trong nhà</span></td>
                                    <td><i class="bx bx-user"></i> 4 người</td>
                                    <td><span class="status-badge status-badge--occupied">Đang phục vụ</span></td>
                                    <td>45 phút</td>
                                    <td>—</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn action-btn--bill" title="Thanh toán"><i
                                                    class="bx bx-receipt"></i></button>
                                            <button class="action-btn" title="Sửa"><i
                                                    class="bx bx-edit-alt"></i></button>
                                            <button class="action-btn action-btn--danger" title="Xóa"><i
                                                    class="bx bx-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="custom-checkbox"><input type="checkbox"><span
                                                class="custom-checkbox__mark"></span></label></td>
                                    <td><strong>Bàn 05</strong></td>
                                    <td><span class="zone-tag zone-tag--indoor">Tầng 1 - Trong nhà</span></td>
                                    <td><i class="bx bx-user"></i> 4 người</td>
                                    <td><span class="status-badge status-badge--reserved">Đã đặt trước</span></td>
                                    <td>—</td>
                                    <td>19:00 - Nguyễn Văn A</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn action-btn--checkin" title="Check-in"><i
                                                    class="bx bx-log-in-circle"></i></button>
                                            <button class="action-btn" title="Sửa"><i
                                                    class="bx bx-edit-alt"></i></button>
                                            <button class="action-btn action-btn--danger" title="Hủy đặt"><i
                                                    class="bx bx-x-circle"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="custom-checkbox"><input type="checkbox"><span
                                                class="custom-checkbox__mark"></span></label></td>
                                    <td><strong>VIP 01</strong></td>
                                    <td><span class="zone-tag zone-tag--vip">Tầng 2 - VIP</span></td>
                                    <td><i class="bx bx-user"></i> 8 người</td>
                                    <td><span class="status-badge status-badge--available">Trống</span></td>
                                    <td>—</td>
                                    <td>—</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn action-btn--seat" title="Xếp khách"><i
                                                    class="bx bx-user-plus"></i></button>
                                            <button class="action-btn" title="Sửa"><i
                                                    class="bx bx-edit-alt"></i></button>
                                            <button class="action-btn action-btn--danger" title="Xóa"><i
                                                    class="bx bx-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="custom-checkbox"><input type="checkbox"><span
                                                class="custom-checkbox__mark"></span></label></td>
                                    <td><strong>Bàn 08</strong></td>
                                    <td><span class="zone-tag zone-tag--indoor">Tầng 1 - Trong nhà</span></td>
                                    <td><i class="bx bx-user"></i> 4 người</td>
                                    <td><span class="status-badge status-badge--maintenance">Bảo trì</span></td>
                                    <td>—</td>
                                    <td>Sửa chữa ghế</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn action-btn--restore" title="Hoàn tất"><i
                                                    class="bx bx-check"></i></button>
                                            <button class="action-btn" title="Sửa"><i
                                                    class="bx bx-edit-alt"></i></button>
                                            <button class="action-btn action-btn--danger" title="Xóa"><i
                                                    class="bx bx-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- XEM NHANH BÀN TRỐNG -->
            <div class="quick-available" id="quickAvailable">
                <div class="quick-available__header">
                    <h3><i class="bx bx-check-circle"></i> Xem nhanh bàn trống — Sẵn sàng xếp khách</h3>
                    <button class="quick-available__toggle" id="toggleQuickView" type="button">
                        <i class="bx bx-chevron-up"></i>
                    </button>
                </div>
                <div class="quick-available__body" id="quickAvailableBody">
                    <div class="quick-available__grid" id="quickGrid">
                        <!-- Sẽ được JS tạo tự động từ các bàn trống -->
                    </div>
                </div>
            </div>

<!-- MODAL: THÊM / SỬA BÀN -->
    <div class="modal-overlay" id="tableModal">
        <div class="modal">
            <div class="modal__header">
                <h2 class="modal__title" id="modalTitle">Thêm bàn mới</h2>
                <button class="modal__close" id="btnCloseModal" type="button"><i class="bx bx-x"></i></button>
            </div>
            <div class="modal__body">
                <form class="modal-form" id="tableForm">
                    <div class="modal-form__row">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Tên / Số bàn <span>*</span></label>
                            <div class="modal-form__input-wrapper">
                                <i class="bx bx-hash"></i>
                                <input type="text" class="modal-form__input" id="inputTableName"
                                    placeholder="VD: Bàn 21, VIP 05..." required>
                            </div>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Sức chứa (người) <span>*</span></label>
                            <div class="modal-form__input-wrapper">
                                <i class="bx bx-group"></i>
                                <input type="number" class="modal-form__input" id="inputCapacity" placeholder="VD: 4"
                                    min="1" max="50" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-form__row">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Khu vực <span>*</span></label>
                            <div class="modal-form__input-wrapper">
                                <i class="bx bx-map"></i>
                                <select class="modal-form__input" id="inputZone" required>
                                    <option value="">Chọn khu vực</option>
                                    <option value="tang1-trong">Tầng 1 - Trong nhà</option>
                                    <option value="tang1-ngoai">Tầng 1 - Ngoài trời</option>
                                    <option value="tang2-vip">Tầng 2 - VIP</option>
                                    <option value="tang2-rieng">Tầng 2 - Phòng riêng</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Trạng thái</label>
                            <div class="modal-form__input-wrapper">
                                <i class="bx bx-info-circle"></i>
                                <select class="modal-form__input" id="inputStatus">
                                    <option value="available">Trống</option>
                                    <option value="occupied">Đang phục vụ</option>
                                    <option value="reserved">Đã đặt trước</option>
                                    <option value="maintenance">Bảo trì</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Ghi chú</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-note"></i>
                            <input type="text" class="modal-form__input" id="inputNote"
                                placeholder="Ghi chú thêm (nếu có)">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal__footer">
                <button class="btn btn--ghost" id="btnCancelTable" type="button">Hủy bỏ</button>
                <button class="btn btn--primary" type="submit" form="tableForm">
                    <i class="bx bx-save"></i> Lưu bàn
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: XẾP KHÁCH -->
    <div class="modal-overlay" id="seatModal">
        <div class="modal modal--sm">
            <div class="modal__header">
                <h2 class="modal__title"><i class="bx bx-user-plus"></i> Xếp khách vào bàn</h2>
                <button class="modal__close" id="btnCloseSeat" type="button"><i class="bx bx-x"></i></button>
            </div>
            <div class="modal__body">
                <div class="seat-info" id="seatInfo">
                    <!-- Thông tin bàn sẽ hiện ở đây -->
                </div>
                <form class="modal-form" id="seatForm">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Số khách</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-group"></i>
                            <input type="number" class="modal-form__input" id="inputGuestCount"
                                placeholder="Số lượng khách" min="1" required>
                        </div>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Tên khách (tùy chọn)</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-user"></i>
                            <input type="text" class="modal-form__input" id="inputGuestName"
                                placeholder="Nhập tên khách hàng">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal__footer">
                <button class="btn btn--ghost" id="btnCancelSeat" type="button">Hủy</button>
                <button class="btn btn--primary" type="submit" form="seatForm">
                    <i class="bx bx-check"></i> Xác nhận xếp khách
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // ============================
        //  ĐỒNG HỒ THỜI GIAN THỰC
        // ============================
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clockTime').textContent = `${h}:${m}:${s}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ============================
        //  CHUYỂN GRID / LIST VIEW
        // ============================
        const viewBtns = document.querySelectorAll('.view-toggle__btn');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');

        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                viewBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                if (btn.dataset.view === 'grid') {
                    gridView.classList.add('active');
                    listView.classList.remove('active');
                } else {
                    listView.classList.add('active');
                    gridView.classList.remove('active');
                }
            });
        });

        // ============================
        //  LỌC BÀN (FILTER)
        // ============================
        const filterZone = document.getElementById('filterZone');
        const filterStatus = document.getElementById('filterStatus');
        const filterCapacity = document.getElementById('filterCapacity');

        function applyFilters() {
            const zone = filterZone.value;
            const status = filterStatus.value;
            const capacity = filterCapacity.value;
            const cards = document.querySelectorAll('.table-card');

            cards.forEach(card => {
                let show = true;
                if (zone && card.dataset.zone !== zone) show = false;
                if (status && card.dataset.status !== status) show = false;
                if (capacity) {
                    const cap = parseInt(card.dataset.capacity);
                    if (capacity === '10' && cap < 10) show = false;
                    else if (capacity !== '10' && cap !== parseInt(capacity)) show = false;
                }
                card.style.display = show ? '' : 'none';
            });

            // Ẩn zone nếu không có bàn nào hiện
            document.querySelectorAll('.zone-section').forEach(zone => {
                const visibleCards = zone.querySelectorAll('.table-card:not([style*="display: none"])');
                zone.style.display = visibleCards.length > 0 ? '' : 'none';
            });
        }

        filterZone.addEventListener('change', applyFilters);
        filterStatus.addEventListener('change', applyFilters);
        filterCapacity.addEventListener('change', applyFilters);

        // ============================
        //  DROPDOWN MENU
        // ============================
        function toggleMenu(id) {
            // Đóng tất cả menu khác
            document.querySelectorAll('.table-card__menu.active').forEach(m => {
                if (m.id !== `menu-${id}`) m.classList.remove('active');
            });
            document.getElementById(`menu-${id}`).classList.toggle('active');
        }

        // Đóng menu khi click ngoài
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.table-card__btn--more') && !e.target.closest('.table-card__menu')) {
                document.querySelectorAll('.table-card__menu.active').forEach(m => m.classList.remove('active'));
            }
        });

        // ============================
        //  ĐỔI TRẠNG THÁI BÀN
        // ============================
        const statusConfig = {
            available: { class: 'table-card--available', text: 'Trống', icon: 'bx-check-circle' },
            occupied: { class: 'table-card--occupied', text: 'Đang phục vụ', icon: 'bx-user-check' },
            reserved: { class: 'table-card--reserved', text: 'Đã đặt trước', icon: 'bx-calendar-check' },
            maintenance: { class: 'table-card--maintenance', text: 'Bảo trì', icon: 'bx-wrench' }
        };

        function changeStatus(id, newStatus) {
            const card = document.querySelector(`.table-card[data-id="${id}"]`);
            if (!card) return;

            // Xóa class cũ
            Object.values(statusConfig).forEach(s => card.classList.remove(s.class));
            // Thêm class mới
            card.classList.add(statusConfig[newStatus].class);
            card.dataset.status = newStatus;

            // Cập nhật text
            const statusText = card.querySelector('.table-card__status-text');
            if (statusText) statusText.textContent = statusConfig[newStatus].text;

            // Cập nhật nút hành động
            updateCardActions(card, newStatus);

            // Đóng menu
            const menu = document.getElementById(`menu-${id}`);
            if (menu) menu.classList.remove('active');

            // Cập nhật thống kê
            updateStats();

            // Cập nhật bàn trống
            updateQuickAvailable();

            // Toast
            showToast(`Bàn đã chuyển sang "${statusConfig[newStatus].text}"`, 'success');
        }

        function updateCardActions(card, status) {
            const actionsDiv = card.querySelector('.table-card__actions');
            const id = card.dataset.id;
            let html = '';

            if (status === 'available') {
                html = `<button class="table-card__btn table-card__btn--seat" onclick="seatGuest(${id})"><i class="bx bx-user-plus"></i> Xếp khách</button>`;
                // Xóa time nếu có
                const timeEl = card.querySelector('.table-card__time');
                if (timeEl) timeEl.remove();
                const resEl = card.querySelector('.table-card__reserved-info');
                if (resEl) resEl.remove();
            } else if (status === 'occupied') {
                html = `<button class="table-card__btn table-card__btn--bill" onclick="billTable(${id})"><i class="bx bx-receipt"></i> Thanh toán</button>`;
            } else if (status === 'reserved') {
                html = `<button class="table-card__btn table-card__btn--checkin" onclick="changeStatus(${id}, 'occupied')"><i class="bx bx-log-in-circle"></i> Check-in</button>`;
            } else if (status === 'maintenance') {
                html = `<button class="table-card__btn table-card__btn--restore" onclick="changeStatus(${id}, 'available')"><i class="bx bx-check"></i> Hoàn tất</button>`;
                const timeEl = card.querySelector('.table-card__time');
                if (timeEl) timeEl.remove();
            }

            html += `<button class="table-card__btn table-card__btn--more" onclick="toggleMenu(${id})"><i class="bx bx-dots-vertical-rounded"></i></button>`;
            actionsDiv.innerHTML = html;
        }

        // ============================
        //  CẬP NHẬT THỐNG KÊ
        // ============================
        function updateStats() {
            const cards = document.querySelectorAll('.table-card');
            let total = 0, available = 0, occupied = 0, reserved = 0, maintenance = 0;
            cards.forEach(card => {
                total++;
                switch (card.dataset.status) {
                    case 'available': available++; break;
                    case 'occupied': occupied++; break;
                    case 'reserved': reserved++; break;
                    case 'maintenance': maintenance++; break;
                }
            });
            document.getElementById('statTotal').textContent = total;
            document.getElementById('statAvailable').textContent = available;
            document.getElementById('statOccupied').textContent = occupied;
            document.getElementById('statReserved').textContent = reserved;
            document.getElementById('statMaintenance').textContent = maintenance;
        }

        // ============================
        //  XEM NHANH BÀN TRỐNG
        // ============================
        function updateQuickAvailable() {
            const grid = document.getElementById('quickGrid');
            const available = document.querySelectorAll('.table-card[data-status="available"]');

            if (available.length === 0) {
                grid.innerHTML = '<p class="quick-available__empty"><i class="bx bx-info-circle"></i> Không có bàn trống nào lúc này.</p>';
                return;
            }

            let html = '';
            available.forEach(card => {
                const name = card.querySelector('.table-card__number').textContent;
                const cap = card.dataset.capacity;
                const zone = card.dataset.zone;
                const zoneLabels = {
                    'tang1-trong': 'T1 - Trong nhà',
                    'tang1-ngoai': 'T1 - Ngoài trời',
                    'tang2-vip': 'T2 - VIP',
                    'tang2-rieng': 'T2 - P.Riêng'
                };
                html += `
                    <div class="quick-item" onclick="seatGuest(${card.dataset.id})">
                        <div class="quick-item__name">${name}</div>
                        <div class="quick-item__meta">
                            <span><i class="bx bx-user"></i> ${cap}</span>
                            <span><i class="bx bx-map"></i> ${zoneLabels[zone] || zone}</span>
                        </div>
                        <button class="quick-item__btn"><i class="bx bx-user-plus"></i></button>
                    </div>`;
            });
            grid.innerHTML = html;
        }
        updateQuickAvailable();

        // Toggle quick view
        document.getElementById('toggleQuickView').addEventListener('click', () => {
            const body = document.getElementById('quickAvailableBody');
            const icon = document.querySelector('#toggleQuickView i');
            body.classList.toggle('collapsed');
            icon.classList.toggle('bx-chevron-up');
            icon.classList.toggle('bx-chevron-down');
        });

        // ============================
        //  MODAL: THÊM / SỬA BÀN
        // ============================
        const tableModal = document.getElementById('tableModal');
        const btnAddTable = document.getElementById('btnAddTable');
        const btnCloseModal = document.getElementById('btnCloseModal');
        const btnCancelTable = document.getElementById('btnCancelTable');

        function openTableModal(title = 'Thêm bàn mới') {
            document.getElementById('modalTitle').textContent = title;
            tableModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeTableModal() {
            tableModal.classList.remove('active');
            document.body.style.overflow = '';
            document.getElementById('tableForm').reset();
        }

        btnAddTable.addEventListener('click', () => openTableModal());
        btnCloseModal.addEventListener('click', closeTableModal);
        btnCancelTable.addEventListener('click', closeTableModal);
        tableModal.addEventListener('click', (e) => { if (e.target === tableModal) closeTableModal(); });

        function editTable(id) {
            const card = document.querySelector(`.table-card[data-id="${id}"]`);
            if (!card) return;
            document.getElementById('inputTableName').value = card.querySelector('.table-card__number').textContent;
            document.getElementById('inputCapacity').value = card.dataset.capacity;
            document.getElementById('inputZone').value = card.dataset.zone;
            document.getElementById('inputStatus').value = card.dataset.status;
            openTableModal('Chỉnh sửa bàn');
            // Đóng dropdown
            document.querySelectorAll('.table-card__menu.active').forEach(m => m.classList.remove('active'));
        }

        // ============================
        //  MODAL: XẾP KHÁCH
        // ============================
        const seatModal = document.getElementById('seatModal');
        const btnCloseSeat = document.getElementById('btnCloseSeat');
        const btnCancelSeat = document.getElementById('btnCancelSeat');

        function seatGuest(id) {
            const card = document.querySelector(`.table-card[data-id="${id}"]`);
            if (!card) return;
            const name = card.querySelector('.table-card__number').textContent;
            const cap = card.dataset.capacity;
            document.getElementById('seatInfo').innerHTML = `
                <div class="seat-info__card">
                    <i class="bx bx-chair"></i>
                    <div>
                        <strong>${name}</strong>
                        <span>Sức chứa: ${cap} người</span>
                    </div>
                </div>`;
            document.getElementById('inputGuestCount').max = cap;
            seatModal.dataset.tableId = id;
            seatModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSeatModal() {
            seatModal.classList.remove('active');
            document.body.style.overflow = '';
            document.getElementById('seatForm').reset();
        }

        btnCloseSeat.addEventListener('click', closeSeatModal);
        btnCancelSeat.addEventListener('click', closeSeatModal);
        seatModal.addEventListener('click', (e) => { if (e.target === seatModal) closeSeatModal(); });

        document.getElementById('seatForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = seatModal.dataset.tableId;
            changeStatus(id, 'occupied');
            closeSeatModal();
            showToast('Đã xếp khách thành công!', 'success');
        });

        // ============================
        //  XÓA BÀN
        // ============================
        function deleteTable(id) {
            if (confirm('Bạn có chắc muốn xóa bàn này?')) {
                const card = document.querySelector(`.table-card[data-id="${id}"]`);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.transform = 'scale(0.8)';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        updateStats();
                        updateQuickAvailable();
                        showToast('Đã xóa bàn thành công!', 'success');
                    }, 300);
                }
            }
            document.querySelectorAll('.table-card__menu.active').forEach(m => m.classList.remove('active'));
        }

        // ============================
        //  THANH TOÁN (placeholder)
        // ============================
        function billTable(id) {
            showToast(`Chuyển đến trang thanh toán cho bàn #${id}`, 'info');
        }

        // ============================
        //  TOAST NOTIFICATIONS
        // ============================
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast--${type}`;
            const icons = { success: 'bx-check-circle', error: 'bx-error-circle', info: 'bx-info-circle' };
            toast.innerHTML = `<i class="bx ${icons[type] || icons.info}"></i><span>${message}</span>`;
            container.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('show'));
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ============================
        //  CHECK ALL (List View)
        // ============================
        const checkAllTables = document.getElementById('checkAllTables');
        if (checkAllTables) {
            checkAllTables.addEventListener('change', () => {
                document.querySelectorAll('.tables-table tbody input[type="checkbox"]').forEach(cb => {
                    cb.checked = checkAllTables.checked;
                });
            });
        }

        // ============================
        //  FORM SUBMIT: THÊM BÀN
        // ============================
        document.getElementById('tableForm').addEventListener('submit', (e) => {
            e.preventDefault();
            showToast('Đã lưu bàn thành công!', 'success');
            closeTableModal();
        });
    </script>

    <!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
    </main>
  </div>

</body>

</html>
