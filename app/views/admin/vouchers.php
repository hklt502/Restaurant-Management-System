<?php
$pageTitle = 'Quản lý Voucher & Khuyến mãi - Restaurant Management System';
$pageCss = '../../../public/asset/css/admin/vouchers.css';
$activePage = 'vouchers';
include_once __DIR__ . '/../layouts/admin_header.php';
?>

            <!-- PAGE TITLE -->
            <div class="page-title">
                <div>
                    <h1 class="page-title__heading">Quản lý Voucher</h1>
                    <p class="page-title__desc">Tạo và quản lý các mã giảm giá, chương trình khuyến mãi cho khách hàng.
                    </p>
                </div>
                <button class="btn btn--primary" id="btnAddVoucher" type="button">
                    <i class="bx bx-plus"></i> Tạo Voucher mới
                </button>
            </div>

            <!-- STAT CARDS -->
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--blue"><i class="bx bx-purchase-tag"></i></div>
                    </div>
                    <p class="stat-card__label">Tổng số Voucher</p>
                    <p class="stat-card__value">45</p>
                </div>
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--green"><i class="bx bx-check-shield"></i></div>
                        <span class="stat-card__badge stat-card__badge--up">Active</span>
                    </div>
                    <p class="stat-card__label">Đang hoạt động</p>
                    <p class="stat-card__value">12</p>
                </div>
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--orange"><i class="bx bx-share-alt"></i></div>
                    </div>
                    <p class="stat-card__label">Lượt đã sử dụng (Tháng)</p>
                    <p class="stat-card__value">342</p>
                </div>
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--red"><i class="bx bx-time-five"></i></div>
                    </div>
                    <p class="stat-card__label">Sắp hết hạn</p>
                    <p class="stat-card__value">3</p>
                </div>
            </div>

            <!-- VOUCHERS CARD -->
            <div class="vouchers-card">
                <!-- TOOLBAR -->
                <div class="toolbar">
                    <div class="toolbar__left">
                        <div class="toolbar__search">
                            <i class="bx bx-search"></i>
                            <input type="text" placeholder="Tìm mã voucher...">
                        </div>
                        <div class="toolbar__filter">
                            <select class="toolbar__select">
                                <option value="">Tất cả loại</option>
                                <option value="percent">Giảm theo %</option>
                                <option value="fixed">Giảm tiền mặt</option>
                                <option value="gift">Tặng món</option>
                            </select>
                        </div>
                        <div class="toolbar__filter">
                            <select class="toolbar__select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="scheduled">Sắp diễn ra</option>
                                <option value="expired">Đã hết hạn</option>
                                <option value="disabled">Đã vô hiệu hóa</option>
                            </select>
                        </div>
                    </div>
                    <div class="toolbar__right">
                        <button class="toolbar__btn" type="button" title="Xuất Excel">
                            <i class="bx bx-export"></i> Xuất dữ liệu
                        </button>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="table-wrapper">
                    <table class="vouchers-table">
                        <thead>
                            <tr>
                                <th>
                                    <label class="custom-checkbox">
                                        <input type="checkbox" id="checkAll">
                                        <span class="custom-checkbox__mark"></span>
                                    </label>
                                </th>
                                <th>MÃ VOUCHER</th>
                                <th>LOẠI GIẢM GIÁ</th>
                                <th>ĐIỀU KIỆN</th>
                                <th>ĐÃ DÙNG</th>
                                <th>THỜI GIAN ÁP DỤNG</th>
                                <th>TRẠNG THÁI</th>
                                <th>HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Item 1 -->
                            <tr>
                                <td>
                                    <label class="custom-checkbox">
                                        <input type="checkbox">
                                        <span class="custom-checkbox__mark"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="voucher-info">
                                        <div class="voucher-info__code">TET2026</div>
                                        <p class="voucher-info__name">Khuyến mãi Tết Nguyên Đán</p>
                                    </div>
                                </td>
                                <td>
                                    <span class="type-tag type-tag--percent">Giảm 20%</span>
                                    <div class="voucher-subtext">Tối đa: 100k</div>
                                </td>
                                <td>Đơn từ 500.000đ</td>
                                <td>
                                    <div class="usage-bar">
                                        <div class="usage-bar__text">145 / 500</div>
                                        <div class="usage-bar__track">
                                            <div class="usage-bar__fill" style="width: 29%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="voucher-date">15/01/2026 - 15/02/2026</div>
                                </td>
                                <td><span class="status-badge status-badge--active">Hoạt động</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="Chỉnh sửa"><i
                                                class="bx bx-edit-alt"></i></button>
                                        <button class="action-btn action-btn--warning" title="Tạm ngưng"><i
                                                class="bx bx-pause-circle"></i></button>
                                        <button class="action-btn action-btn--danger" title="Xóa"><i
                                                class="bx bx-trash"></i></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Item 2 -->
                            <tr>
                                <td>
                                    <label class="custom-checkbox">
                                        <input type="checkbox">
                                        <span class="custom-checkbox__mark"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="voucher-info">
                                        <div class="voucher-info__code">WELCOME50</div>
                                        <p class="voucher-info__name">Mừng khách hàng mới</p>
                                    </div>
                                </td>
                                <td>
                                    <span class="type-tag type-tag--fixed">Giảm 50.000đ</span>
                                </td>
                                <td>Đơn từ 200.000đ</td>
                                <td>
                                    <div class="usage-bar">
                                        <div class="usage-bar__text">890 / ∞</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="voucher-date">Không giới hạn</div>
                                </td>
                                <td><span class="status-badge status-badge--active">Hoạt động</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="Chỉnh sửa"><i
                                                class="bx bx-edit-alt"></i></button>
                                        <button class="action-btn action-btn--warning" title="Tạm ngưng"><i
                                                class="bx bx-pause-circle"></i></button>
                                        <button class="action-btn action-btn--danger" title="Xóa"><i
                                                class="bx bx-trash"></i></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Item 3 -->
                            <tr>
                                <td>
                                    <label class="custom-checkbox">
                                        <input type="checkbox">
                                        <span class="custom-checkbox__mark"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="voucher-info">
                                        <div class="voucher-info__code">SINHNHAT</div>
                                        <p class="voucher-info__name">Quà tặng tháng sinh nhật</p>
                                    </div>
                                </td>
                                <td>
                                    <span class="type-tag type-tag--gift">Tặng Món</span>
                                    <div class="voucher-subtext">Bánh kem nhỏ</div>
                                </td>
                                <td>Check-in sinh nhật</td>
                                <td>
                                    <div class="usage-bar">
                                        <div class="usage-bar__text">12 / 100</div>
                                        <div class="usage-bar__track">
                                            <div class="usage-bar__fill" style="width: 12%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="voucher-date">01/09/2026 - 30/09/2026</div>
                                </td>
                                <td><span class="status-badge status-badge--scheduled">Sắp tới</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="Chỉnh sửa"><i
                                                class="bx bx-edit-alt"></i></button>
                                        <button class="action-btn action-btn--warning" title="Tạm ngưng"><i
                                                class="bx bx-pause-circle"></i></button>
                                        <button class="action-btn action-btn--danger" title="Xóa"><i
                                                class="bx bx-trash"></i></button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Item 4 -->
                            <tr>
                                <td>
                                    <label class="custom-checkbox">
                                        <input type="checkbox">
                                        <span class="custom-checkbox__mark"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="voucher-info">
                                        <div class="voucher-info__code">SUMMER2025</div>
                                        <p class="voucher-info__name">Giải nhiệt mùa hè</p>
                                    </div>
                                </td>
                                <td>
                                    <span class="type-tag type-tag--percent">Giảm 10%</span>
                                    <div class="voucher-subtext">Không giới hạn</div>
                                </td>
                                <td>Đơn từ 0đ</td>
                                <td>
                                    <div class="usage-bar">
                                        <div class="usage-bar__text">500 / 500</div>
                                        <div class="usage-bar__track">
                                            <div class="usage-bar__fill" style="width: 100%; background: var(--text3)">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="voucher-date">01/06/2025 - 31/08/2025</div>
                                </td>
                                <td><span class="status-badge status-badge--expired">Đã hết hạn</span></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="Xem"><i class="bx bx-show"></i></button>
                                        <button class="action-btn action-btn--danger" title="Xóa"><i
                                                class="bx bx-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="pagination">
                    <div class="pagination__info">
                        Hiển thị <strong>1 - 4</strong> trong <strong>45</strong> voucher
                    </div>
                    <div class="pagination__controls">
                        <button class="pagination__btn" disabled><i class="bx bx-chevron-left"></i></button>
                        <button class="pagination__btn pagination__btn--active">1</button>
                        <button class="pagination__btn">2</button>
                        <button class="pagination__btn">3</button>
                        <span class="pagination__dots">...</span>
                        <button class="pagination__btn">5</button>
                        <button class="pagination__btn"><i class="bx bx-chevron-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- MODAL: THÊM / SỬA VOUCHER -->
            <div class="modal-overlay" id="voucherModal">
                <div class="modal">
                    <div class="modal__header">
                        <h2 class="modal__title">Tạo Voucher Mới</h2>
                        <button class="modal__close" id="btnCloseModal" type="button"><i class="bx bx-x"></i></button>
                    </div>
                    <div class="modal__body">
                        <form class="modal-form" id="voucherForm">
                            <!-- General Info -->
                            <h3 class="modal-form__section-title">Thông tin cơ bản</h3>
                            <div class="modal-form__row">
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Mã Voucher (Code) <span>*</span></label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-barcode-reader"></i>
                                        <input type="text" class="modal-form__input" placeholder="VD: TET2026, SUMMER..."
                                            style="text-transform: uppercase" required>
                                    </div>
                                </div>
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Tên chương trình</label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-text"></i>
                                        <input type="text" class="modal-form__input" placeholder="VD: Khuyến mãi Tết">
                                    </div>
                                </div>
                            </div>

                            <!-- Discount Type -->
                            <h3 class="modal-form__section-title">Thiết lập giảm giá</h3>
                            <div class="modal-form__row">
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Loại giảm giá <span>*</span></label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-purchase-tag-alt"></i>
                                        <select class="modal-form__input" id="discountType" required>
                                            <option value="percent">Giảm theo %</option>
                                            <option value="fixed">Giảm tiền mặt</option>
                                            <option value="gift">Tặng món</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-form__group" id="discountValueGroup">
                                    <label class="modal-form__label" id="discountLabel">Mức giảm (%) <span>*</span></label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-dollar-circle"></i>
                                        <input type="number" class="modal-form__input" placeholder="VD: 20" required>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-form__row">
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Đơn tối thiểu (VNĐ)</label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-up-arrow-circle"></i>
                                        <input type="number" class="modal-form__input" placeholder="0 = Không yêu cầu">
                                    </div>
                                </div>
                                <div class="modal-form__group" id="maxDiscountGroup">
                                    <label class="modal-form__label">Giảm tối đa (VNĐ)</label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-down-arrow-circle"></i>
                                        <input type="number" class="modal-form__input" placeholder="0 = Không giới hạn">
                                    </div>
                                </div>
                            </div>

                            <!-- Condition -->
                            <h3 class="modal-form__section-title">Thời gian & Giới hạn</h3>
                            <div class="modal-form__row">
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Từ ngày</label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-calendar"></i>
                                        <input type="date" class="modal-form__input">
                                    </div>
                                </div>
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Đến ngày</label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-calendar-event"></i>
                                        <input type="date" class="modal-form__input">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-form__group">
                                <label class="modal-form__label">Số lượng giới hạn</label>
                                <div class="modal-form__input-wrapper">
                                    <i class="bx bx-group"></i>
                                    <input type="number" class="modal-form__input" placeholder="Để trống nếu không giới hạn">
                                </div>
                            </div>

                            <div class="modal-form__group">
                                <label class="modal-form__label">Trạng thái</label>
                                <div class="toggle-group">
                                    <label class="toggle">
                                        <input type="checkbox" checked>
                                        <span class="toggle__slider"></span>
                                    </label>
                                    <span class="toggle-group__text">Kích hoạt Voucher ngay</span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal__footer">
                        <button class="btn btn--ghost" id="btnCancel" type="button">Hủy bỏ</button>
                        <button class="btn btn--primary" type="submit" form="voucherForm">
                            <i class="bx bx-save"></i> Lưu Voucher
                        </button>
                    </div>
                </div>
            </div>

            <script>
                // Modal Logic
                const modal = document.getElementById('voucherModal');
                const btnAdd = document.getElementById('btnAddVoucher');
                const btnClose = document.getElementById('btnCloseModal');
                const btnCancel = document.getElementById('btnCancel');

                function openModal() { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
                function closeModal() { modal.classList.remove('active'); document.body.style.overflow = ''; }

                btnAdd.addEventListener('click', openModal);
                btnClose.addEventListener('click', closeModal);
                btnCancel.addEventListener('click', closeModal);
                modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

                // Check all
                const checkAll = document.getElementById('checkAll');
                const rowChecks = document.querySelectorAll('.vouchers-table tbody input[type="checkbox"]');
                checkAll.addEventListener('change', () => {
                    rowChecks.forEach(cb => cb.checked = checkAll.checked);
                });

                // Dynamic Form Fields based on Discount Type
                const discountType = document.getElementById('discountType');
                const discountLabel = document.getElementById('discountLabel');
                const maxDiscountGroup = document.getElementById('maxDiscountGroup');

                discountType.addEventListener('change', function () {
                    if (this.value === 'percent') {
                        discountLabel.innerHTML = 'Mức giảm (%) <span>*</span>';
                        maxDiscountGroup.style.display = 'block';
                    } else if (this.value === 'fixed') {
                        discountLabel.innerHTML = 'Số tiền giảm (VNĐ) <span>*</span>';
                        maxDiscountGroup.style.display = 'none';
                    } else {
                        discountLabel.innerHTML = 'Tên món tặng <span>*</span>';
                        maxDiscountGroup.style.display = 'none';
                    }
                });
            </script>

<?php
include_once __DIR__ . '/../layouts/admin_footer.php';
?>

