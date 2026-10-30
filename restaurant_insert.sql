USE restaurant;

-- ==========================================================================
-- 1. ADMIN (5 records)
-- ==========================================================================
INSERT INTO `admin` (`adminId`, `username`, `passwordHash`, `adminName`, `adminPhone`) VALUES
(1, 'admin01',   MD5('admin123'),   'Hồ Kim Lưu Thống',  '0901234567'),
(2, 'manager01', MD5('manager123'), 'Nguyễn Văn Minh',   '0912345678'),
(3, 'staff01',   MD5('staff123'),   'Trần Thị Lan',      '0923456789'),
(4, 'staff02',   MD5('staff456'),   'Lê Quốc Hùng',      '0934567890'),
(5, 'staff03',   MD5('staff789'),   'Phạm Ngọc Anh',     '0945678901');

-- ==========================================================================
-- 2. CUSTOMER (5 records)
-- ==========================================================================
INSERT INTO `customer` (`customerId`, `customerName`, `customerPhone`, `customerEmail`, `passwordHash`, `birthDate`, `gender`, `address`, `visitCount`) VALUES
(1, 'Nguyễn Hữu An',      '0931111111', 'an.nguyen@gmail.com',   MD5('pass123'), '1995-05-15', 'MALE',   '123 Lê Lợi, Q1, TP.HCM',        5),
(2, 'Lê Thị Hương Giang', '0932222222', 'giang.le@gmail.com',    MD5('pass123'), '1998-10-20', 'FEMALE', '456 Hai Bà Trưng, Q3, TP.HCM',  2),
(3, 'Trần Quốc Bảo',      '0933333333', 'bao.tran@gmail.com',    MD5('pass123'), '1990-12-01', 'MALE',   '789 Nguyễn Văn Cừ, Q5, TP.HCM', 10),
(4, 'Phạm Thị Ngọc Linh', '0934444444', 'linh.pham@gmail.com',   MD5('pass123'), '2000-02-14', 'FEMALE', '101 Võ Văn Tần, Q3, TP.HCM',    1),
(5, 'Hoàng Minh Tuấn',    '0935555555', 'tuan.hoang@gmail.com',  MD5('pass123'), '1988-08-08', 'MALE',   '202 Trần Hưng Đạo, Q1, TP.HCM', 3);

-- ==========================================================================
-- 3. CATEGORY (6 records)
-- ==========================================================================
INSERT INTO `category` (`categoryId`, `categoryName`, `description`) VALUES
(1, 'Thịt',        'Các món từ thịt bò, thịt cừu, thịt lợn'),
(2, 'Hải sản',     'Tôm, cua, mực, cá tươi sống'),
(3, 'Viên thả lẩu', 'Các loại viên thả lẩu đặc biệt'),
(4, 'Rau củ',      'Rau xanh, nấm tươi các loại'),
(5, 'Đồ uống',     'Nước ngọt, bia, nước ép'),
(6, 'Nội tạng',    'Lòng, tim, gan bò và lợn');

-- ==========================================================================
-- 4. FOOD (17 records) — foodStatus: AVAILABLE | UNAVAILABLE
-- ==========================================================================
INSERT INTO `food` (`foodId`, `categoryId`, `foodName`, `foodDescription`, `foodPrice`, `foodDiscount`, `foodStatus`, `isFeatured`, `imageURL`) VALUES
-- Thịt
(1,  1, 'Bò Mỹ Thượng Hạng',   'Thịt bò Wagyu nhập khẩu, thái lát mỏng',  350000, 0,  'AVAILABLE',   1, '/public/asset/img/thitbo.png'),
(2,  1, 'Thịt Cừu Úc',         'Cừu tươi nhập khẩu từ Úc',                280000, 0,  'AVAILABLE',   1, '/public/asset/img/thitcuu.jpg'),
(3,  1, 'Thịt Lợn Ba Chỉ',     'Ba chỉ lợn tươi thái lát',                120000, 0,  'AVAILABLE',   0, '/public/asset/img/bachi.jpg'),
(4,  1, 'Xúc Xích Đức',        'Xúc xích Đức nhập khẩu',                  80000,  0,  'AVAILABLE',   0, '/public/asset/img/xucxich.jpg'),
-- Hải sản
(5,  2, 'Tôm Sú Tươi',         'Tôm sú tươi sống, size lớn',              180000, 0,  'AVAILABLE',   1, '/public/asset/img/tomsu.jpg'),
(6,  2, 'Mực Ống Tươi',        'Mực ống tươi, sạch',                      150000, 0,  'AVAILABLE',   0, '/public/asset/img/mucong.jpg'),
(7,  2, 'Cua Biển',            'Cua biển tươi sống',                      250000, 0,  'AVAILABLE',   1, '/public/asset/img/cuabien.jpg'),
-- Viên
(8,  3, 'Sủi Cảo Tôm',         'Sủi cảo nhân tôm tươi',                   70000,  0,  'AVAILABLE',   0, '/public/asset/img/suicao.jpg'),
(9,  3, 'Viên Bò Phô Mai',     'Viên bò nhân phô mai tan chảy',           75000,  0,  'AVAILABLE',   1, '/public/asset/img/vienbo.jpg'),
(10, 3, 'Chả Cá Thác Lác',     'Chả cá truyền thống',                     65000,  0,  'AVAILABLE',   0, '/public/asset/img/chaca.jpg'),
-- Rau
(11, 4, 'Rau Muống',           'Rau muống tươi, sạch',                    30000,  0,  'AVAILABLE',   0, '/public/asset/img/raumuong.jpg'),
(12, 4, 'Bắp Cải Xanh',        'Bắp cải tươi',                            25000,  0,  'AVAILABLE',   0, '/public/asset/img/bapcai.jpg'),
(13, 4, 'Nấm Kim Châm',        'Nấm kim châm tươi',                       40000,  0,  'AVAILABLE',   0, '/public/asset/img/namkimcham.jpg'),
-- Nước
(14, 5, 'Nước Suối',           'Nước suối 500ml',                         15000,  0,  'AVAILABLE',   0, '/public/asset/img/nuocsuoi.jpg'),
(15, 5, 'Bia Heineken',        'Bia Heineken lon 330ml',                  35000,  0,  'AVAILABLE',   0, '/public/asset/img/heineken.jpg'),
-- Khác
(16, 6, 'Lòng Lợn',            'Lòng lợn sạch, đã sơ chế',                90000,  0,  'AVAILABLE',   0, '/public/asset/img/longlon.jpg'),
(17, 6, 'Tim Bò',              'Tim bò tươi thái lát',                    110000, 0,  'UNAVAILABLE', 0, '/public/asset/img/timbo.jpg');

-- ==========================================================================
-- 5. SERVICE_TABLE (10 records) — status: AVAILABLE | OCCUPIED | RESERVED
-- ==========================================================================
INSERT INTO `service_table` (`tableId`, `tableNumber`, `area`, `capacity`, `status`) VALUES
(1,  'T01', 'Trong nhà',  4,  'AVAILABLE'),
(2,  'T02', 'Trong nhà',  4,  'OCCUPIED'),
(3,  'T03', 'Trong nhà',  6,  'OCCUPIED'),
(4,  'T04', 'Trong nhà',  4,  'AVAILABLE'),
(5,  'T05', 'Trong nhà',  2,  'AVAILABLE'),
(6,  'T06', 'Ngoài trời', 6,  'RESERVED'),
(7,  'T07', 'Ngoài trời', 4,  'AVAILABLE'),
(8,  'T08', 'Ngoài trời', 8,  'OCCUPIED'),
(9,  'T09', 'VIP',        10, 'AVAILABLE'),
(10, 'T10', 'VIP',        10, 'RESERVED');

-- ==========================================================================
-- 6. PROMOTION (5 records)
-- ==========================================================================
INSERT INTO `promotion` (`promotionId`, `title`, `badgeText`, `description`, `expiryInfo`, `targetAudience`, `imageURL`, `isActive`) VALUES
(1, 'Tặng voucher 100k cho nhóm trên 5 người', 'Tặng voucher 100k',   'Áp dụng cho khách hàng đi theo nhóm từ 5 người trở lên.',   'Hạn sử dụng: 31/12/2026', 'Mọi khách hàng',  '/public/asset/img/promo1.jpg', 1),
(2, 'Giảm 15% tổng bill cho hội viên mới',     'Giảm 15% tổng bill',  'Tạo tài khoản và nhận ngay ưu đãi 15% cho hóa đơn đầu tiên.', 'Không giới hạn thời gian', 'Khách hàng mới', '/public/asset/img/promo2.jpg', 1),
(3, 'Đi 4 tính 3 ngày trong tuần',            'Đi 4 tính 3',         'Chỉ áp dụng từ Thứ 2 đến Thứ 6.',                           'Hạn sử dụng: 31/12/2026', 'Mọi khách hàng',  '/public/asset/img/promo3.jpg', 1),
(4, 'Ưu đãi tháng sinh nhật',                  'Giảm 20% sinh nhật',  'Vui lòng xuất trình CCCD để được giảm 20% tháng sinh nhật.', 'Tháng sinh nhật',       'Thành viên',      '/public/asset/img/promo4.jpg', 1),
(5, 'Giờ vàng 16h - 18h',                      'Bia giảm 50%',        'Tất cả các loại bia giảm 50% trong khung giờ vàng.',          'Hàng ngày 16:00-18:00',   'Mọi khách hàng',  '/public/asset/img/promo5.jpg', 1);

-- ==========================================================================
-- 7. VOUCHER (6 records) — isDeleted: 0 (active) | 1 (deleted)
-- ==========================================================================
INSERT INTO `voucher` (`voucherId`, `promotionId`, `voucherCode`, `discountType`, `discountValue`, `minOrderValue`, `expiryDate`, `usageLimit`, `usedCount`, `isActive`, `isDeleted`) VALUES
(1, 2, 'WELCOME10',  'PERCENT', 10.00,  200000, '2026-12-31', 100, 5,  1, 0),
(2, 1, 'SALE50K',    'FIXED',   50000,  300000, '2026-11-30', 50,  12, 1, 0),
(3, 3, 'VIP20',      'PERCENT', 20.00,  500000, '2026-12-31', 30,  3,  1, 0),
(4, 4, 'BIRTHDAY15', 'PERCENT', 15.00,  150000, '2026-12-31', 200, 8,  1, 0),
(5, 5, 'SUMMER30K',  'FIXED',   30000,  200000, '2026-10-31', 80,  20, 1, 0),
(6, NULL, 'EXPIRED5',   'PERCENT', 5.00,   100000, '2025-01-01', 10,  10, 0, 1),
(7, 1, 'GROUP100K',  'FIXED',   100000, 600000, '2026-12-31', 50,  0,  1, 0),
(8, 2, 'NEWBIE15',   'PERCENT', 15.00,  250000, '2026-12-31', 100, 0,  1, 0);

-- ==========================================================================
-- 8. RESERVATION (6 records) — status: PENDING | CONFIRMED | CANCELLED
-- ==========================================================================
INSERT INTO `reservation` (`reservationId`, `customerId`, `tableId`, `guestName`, `guestPhone`, `guestEmail`, `reservationDate`, `reservationTime`, `guestCount`, `branch`, `status`, `note`) VALUES
(1, 1, 6,    'Nguyễn Hữu An',      '0931111111', 'an.nguyen@gmail.com',  '2026-10-05', '18:00:00', 8,  'Lê Lợi Q1',      'CONFIRMED', 'Tiệc sinh nhật, vui lòng chuẩn bị bánh kem'),
(2, 3, NULL, 'Trần Quốc Bảo',      '0933333333', 'bao.tran@gmail.com',   '2026-10-10', '19:30:00', 4,  'Bến Thành Q1',   'PENDING',   NULL),
(3, 5, 7,    'Hoàng Minh Tuấn',    '0935555555', 'tuan.hoang@gmail.com', '2026-10-12', '12:00:00', 2,  'Lê Lợi Q1',      'CONFIRMED', NULL),
(4, 2, NULL, 'Lê Thị Hương Giang', '0932222222', 'giang.le@gmail.com',   '2026-10-03', '18:30:00', 4,  'Bến Thành Q1',   'CANCELLED', 'Khách gọi điện hủy bàn'),
(5, 4, 9,    'Phạm Thị Ngọc Linh', '0934444444', 'linh.pham@gmail.com',  '2026-10-15', '19:00:00', 6,  'Lê Lợi Q1',      'CONFIRMED', 'Kỷ niệm ngày cưới'),
(6, NULL, NULL,'Nguyễn Văn A',     '0900000001', NULL,                   '2026-10-20', '11:30:00', 10, 'Chi nhánh Thủ Đức', 'PENDING',  'Đi ăn gia đình, 10 người lớn');

-- ==========================================================================
-- 9. ORDERS (7 records) — status: OPEN | SERVING | PAID
-- ==========================================================================
INSERT INTO `orders` (`orderId`, `customerId`, `adminId`, `tableId`, `reservationId`, `voucherId`, `guestCount`, `subtotal`, `discountAmount`, `totalAmount`, `paymentMethod`, `status`, `orderDate`, `paidAt`) VALUES
(1, 1,    2, 2, NULL, 1,    3, 1250000, 125000, 1125000, 'CASH',     'PAID',    '2026-10-01 11:00:00', '2026-10-01 13:00:00'),
(2, 2,    2, 3, NULL, NULL, 2,  550000,      0,  550000, 'TRANSFER', 'SERVING', '2026-10-01 12:00:00', NULL),
(3, 3,    1, 8, NULL, 2,    5, 1800000,  50000, 1750000, 'CASH',     'SERVING', '2026-10-01 13:00:00', NULL),
(4, 4,    3, 5, NULL, NULL, 2,  320000,      0,  320000, 'CASH',     'PAID',    '2026-10-01 10:30:00', '2026-10-01 11:30:00'),
(5, NULL, 1, 4, NULL, NULL, 1,  165000,      0,  165000, 'CASH',     'OPEN',    '2026-10-01 14:00:00', NULL),
(6, 5,    4, 7, NULL, 4,    4,  980000, 147000,  833000, 'TRANSFER', 'PAID',    '2026-10-01 18:00:00', '2026-10-01 20:30:00'),
(7, 2,    5, 9, NULL, 5,    6, 2100000,  30000, 2070000, 'CASH',     'SERVING', '2026-10-01 19:00:00', NULL);

-- ==========================================================================
-- 10. ORDER_ITEMS (20 records) — itemStatus: ORDER | COOKING | SERVED
-- ==========================================================================
INSERT INTO `order_items` (`orderItemId`, `orderId`, `foodId`, `quantity`, `unitPrice`, `lineTotal`, `itemStatus`, `note`) VALUES
-- Order 1 (PAID)
(1,  1, 1,  2, 350000, 700000, 'SERVED',  NULL),
(2,  1, 8,  2,  70000, 140000, 'SERVED',  NULL),
(3,  1, 11, 2,  30000,  60000, 'SERVED',  NULL),
(4,  1, 15, 5,  35000, 175000, 'SERVED',  NULL),
(5,  1, 14, 4,  15000,  60000, 'SERVED',  NULL),
-- Order 2 (SERVING)
(6,  2, 3,  2, 120000, 240000, 'SERVED',  NULL),
(7,  2, 10, 2,  65000, 130000, 'COOKING', NULL),
(8,  2, 14, 2,  15000,  30000, 'SERVED',  NULL),
-- Order 3 (SERVING)
(9,  3, 5,  3, 180000, 540000, 'SERVED',  NULL),
(10, 3, 6,  2, 150000, 300000, 'COOKING', NULL),
(11, 3, 1,  1, 350000, 350000, 'ORDER',   NULL),
(12, 3, 15, 8,  35000, 280000, 'SERVED',  NULL),
-- Order 4 (PAID)
(13, 4, 8,  2,  70000, 140000, 'SERVED',  NULL),
(14, 4, 14, 4,  15000,  60000, 'SERVED',  NULL),
(15, 4, 13, 3,  40000, 120000, 'SERVED',  NULL),
-- Order 5 (OPEN)
(16, 5, 3,  1, 120000, 120000, 'ORDER',   NULL),
(17, 5, 14, 3,  15000,  45000, 'ORDER',   NULL),
-- Order 6 (PAID)
(18, 6, 2,  2, 280000, 560000, 'SERVED',  NULL),
(19, 6, 9,  4,  75000, 300000, 'SERVED',  NULL),
-- Order 7 (SERVING)
(20, 7, 7,  3, 250000, 750000, 'SERVED',  NULL);

-- ==========================================================================
-- 11. INGREDIENT (10 records)
-- ==========================================================================
INSERT INTO `ingredient` (`ingredientId`, `ingredientName`, `unit`, `quantityInStock`, `minQuantity`) VALUES
(1,  'Thịt Bò Wagyu',     'kg',   100.000,  5.000),
(2,  'Thịt Cừu Úc',       'kg',     1.000,  5.000),
(3,  'Ba Chỉ Lợn',        'kg',     0.000,  5.000),
(4,  'Xúc Xích Đức',      'cái', 1000.000, 10.000),
(5,  'Tôm Sú',            'kg',    20.000,  3.000),
(6,  'Mực Ống',           'kg',    15.000,  3.000),
(7,  'Cua Biển',          'kg',     8.000,  2.000),
(8,  'Bắp Cải Xanh',      'kg',     0.000, 15.000),
(9,  'Rau Muống',         'kg',    10.000,  2.000),
(10, 'Nấm Kim Châm',      'gói',   30.000,  5.000);

-- ==========================================================================
-- 12. INVENTORY_LOG (10 records) — logType: IMPORT | EXPORT
-- ==========================================================================
INSERT INTO `inventory_log` (`logId`, `ingredientId`, `adminId`, `logType`, `quantity`, `note`) VALUES
(1,  1, 1, 'IMPORT', 100.000, 'Nhập kho đầu tháng - Thịt Bò Wagyu'),
(2,  2, 1, 'IMPORT',  10.000, 'Nhập thêm Thịt Cừu Úc'),
(3,  3, 1, 'IMPORT',  20.000, 'Nhập ba chỉ lợn tươi'),
(4,  3, 2, 'EXPORT',  20.000, 'Xuất cho bếp - đã hết hàng'),
(5,  8, 1, 'IMPORT',  30.000, 'Nhập bắp cải xanh'),
(6,  8, 2, 'EXPORT',  30.000, 'Xuất cho bếp - đã hết hàng'),
(7,  5, 1, 'IMPORT',  25.000, 'Nhập tôm sú'),
(8,  5, 2, 'EXPORT',   5.000, 'Xuất bếp phục vụ đơn hàng bàn T08'),
(9,  4, 1, 'IMPORT', 500.000, 'Nhập xúc xích Đức'),
(10, 6, 3, 'IMPORT',  15.000, 'Nhập mực ống tươi');

-- ==========================================================================
-- 13. CUSTOMER_VOUCHER (Ví voucher của từng khách hàng)
-- ==========================================================================
INSERT INTO `customer_voucher` (`id`, `customerId`, `voucherId`, `status`, `assignedAt`, `usedAt`) VALUES
(1, 1, 1, 'USED',   '2026-09-20 10:00:00', '2026-10-01 13:00:00'),
(2, 1, 4, 'UNUSED', '2026-10-01 10:00:00', NULL),
(3, 3, 2, 'USED',   '2026-09-15 08:30:00', '2026-10-01 13:00:00'),
(4, 3, 1, 'UNUSED', '2026-10-01 09:00:00', NULL),
(5, 3, 3, 'UNUSED', '2026-10-01 09:00:00', NULL),
(6, 3, 4, 'UNUSED', '2026-10-01 09:00:00', NULL),
(7, 3, 5, 'UNUSED', '2026-10-01 09:00:00', NULL),
(8, 4, 4, 'UNUSED', '2026-10-01 10:00:00', NULL);
