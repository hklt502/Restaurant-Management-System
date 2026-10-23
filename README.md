# HỆ THỐNG QUẢN LÝ NHÀ HÀNG (BUFFET GEMORI)
> **Đề tài Môn học:** Lập trình Web  
> **Nhóm thực hiện:** Nhóm 3  
> **Công nghệ:** PHP (MVC), MySQL, HTML5, CSS3 (BEM), JavaScript, Chart.js, Boxicons  

---

## 📌 I. GIỚI THIỆU DỰ ÁN

**Hệ thống Quản lý Nhà hàng Buffet Gemori** là ứng dụng web toàn diện phục vụ hoạt động vận hành và kinh doanh ẩm thực. Hệ thống bao gồm 2 phân hệ độc lập:
1. **Phân hệ Khách hàng (Client Portal):** Giới thiệu thương hiệu, duyệt thực đơn, săn mã voucher ưu đãi, đặt bàn giữ chỗ trực tuyến và quản lý tài khoản thành viên.
2. **Phân hệ Quản trị (Admin Portal):** Bảng điều khiển trung tâm giúp quản lý theo dõi doanh thu theo thời gian thực, quản lý sơ đồ bàn, phục vụ gọi món tại bàn, kiểm kê kho nguyên liệu, phát hành voucher và báo cáo thống kê.

---

## 📂 II. CẤU TRÚC THƯ MỤC HỆ THỐNG (MÔ HÌNH MVC)

Dự án được tổ chức theo cấu trúc MVC chuẩn, phân tách rõ ràng giữa giao diện, nghiệp vụ và cơ sở dữ liệu:

```text
Restaurant-Management-System/
│
├── app/                                    # Thư mục mã nguồn chính (Backend & View)
│   ├── config/                             # Cấu hình hệ thống & CSDL (database.php, config.php)
│   ├── core/                               # Các lớp lõi (Router, Base Controller, Base Model/Database)
│   ├── controllers/                        # Tầng xử lý nghiệp vụ (Controller)
│   │   ├── admin/                          # Controllers cho phân hệ Admin (Menu, Order, Table,...)
│   │   └── client/                         # Controllers cho phân hệ Khách hàng (Home, Booking, Food,...)
│   ├── models/                             # Tầng tương tác CSDL (FoodModel, OrderModel, TableModel,...)
│   ├── helpers/                            # Các hàm tiện ích dùng chung (Session, Auth, Upload,...)
│   └── views/                              # Tầng giao diện hiển thị (View)
│       ├── layouts/                        # Các thành phần giao diện dùng chung (Head, Header, Footer, Sidebar)
│       │   ├── client_head.php             # Thẻ <head>, font chữ, stylesheet cho khách hàng
│       │   ├── client_header.php           # Thanh điều hướng menu (#header) cho khách hàng
│       │   ├── client_footer.php           # Chân trang (.footer) cho khách hàng
│       │   ├── admin_head.php              # Thẻ <head>, Boxicons, font chữ Inter cho admin
│       │   ├── admin_sidebar.php           # Thanh menu điều hướng bên trái (.sidebar) cho admin
│       │   ├── admin_header.php            # Thanh tìm kiếm, đồng hồ, thông báo (.header) cho admin
│       │   └── admin_footer.php            # Đóng thẻ vùng nội dung chính và chân trang admin
│       ├── admin/                          # Giao diện các trang quản trị (.php)
│       │   ├── overview.php                # Tổng quan Dashboard & KPI
│       │   ├── menu.php                    # Quản lý Menu & Danh mục món
│       │   ├── tables.php                  # Quản lý Sơ đồ bàn & Đặt bàn
│       │   ├── orders.php                  # Quản lý Đơn hàng & Gọi món
│       │   ├── warehouse.php               # Quản lý Kho nguyên liệu & Tồn kho
│       │   ├── vouchers.php                # Quản lý Voucher & Khuyến mãi
│       │   ├── users.php                   # Quản lý Người dùng & Phân quyền
│       │   ├── statistics.php              # Báo cáo Thống kê & Biểu đồ
│       │   └── loginv.php                  # Đăng nhập Quản trị viên
│       └── client/                         # Giao diện các trang khách hàng (.php)
│           ├── trangchu.php                # Trang chủ giới thiệu
│           ├── thucdon.php                 # Danh sách thực đơn món ăn
│           ├── datban.php                  # Form đặt bàn trực tuyến
│           ├── khuyenmai.php               # Danh sách ưu đãi & Voucher
│           ├── dangnhap.php                # Đăng nhập khách hàng
│           ├── dangky.php                  # Đăng ký tài khoản thành viên
│           ├── canhan.php                  # Trang hồ sơ cá nhân
│           └── doimatkhau.php              # Đổi mật khẩu tài khoản
│
├── public/                                 # Thư mục công khai (Web Root)
│   └── asset/                              # Tài nguyên tĩnh
│       ├── css/                            # File định kiểu giao diện
│       │   ├── admin/                      # CSS cho từng trang Admin (BEM)
│       │   └── client/                     # CSS cho phân hệ Client (style.css)
│       ├── js/                             # JavaScript xử lý phía Client
│       │   ├── admin/                      # JS chức năng Admin
│       │   └── client/                     # JS tương tác Client
│       └── img/                            # Hình ảnh món ăn, logo, banner
│
├── restaurant.sql                          # Cấu trúc bảng CSDL MySQL
├── restaurant_insert.sql                   # Dữ liệu mẫu ban đầu (Menu, Bàn, Tài khoản)
└── README.md                               # Tài liệu hướng dẫn dự án
```

---

## 🧩 III. CÁC THÀNH PHẦN GIAO DIỆN DÙNG CHUNG (LAYOUTS)

Hệ thống đã được tách các khối **Head**, **Header**, **Footer**, **Sidebar** dùng chung vào thư mục `app/views/layouts/` để tránh lặp mã nguồn:

### 1. Phân hệ Khách hàng (`Client`):
*   `client_head.php`: Chứa thẻ `<!DOCTYPE html>`, `<head>`, meta responsive, font Google (`Playfair Display`, `Lora`, `DM Sans`) và link CSS `public/asset/css/client/style.css`.
*   `client_header.php`: Chứa khối `#header` với logo, menu điều hướng đa cấp, nút đặt bàn và menu mobile responsive.
*   `client_footer.php`: Chứa khối `.footer`, thông tin liên hệ, giờ mở cửa, liên kết nhanh và đóng các thẻ `</body></html>`.

### 2. Phân hệ Quản trị (`Admin`):
*   `admin_head.php`: Chứa `<head>`, font `Inter`, thư viện icon `Boxicons` và hỗ trợ nạp linh hoạt CSS riêng của từng trang admin.
*   `admin_sidebar.php`: Thanh menu cố định bên trái (`.sidebar`) với 4 nhóm chức năng (Chung, Vận hành, Marketing & Kho, Hệ thống) và thông tin tài khoản admin.
*   `admin_header.php`: Thanh tiêu đề trên cùng (`.header`) gồm ô tìm kiếm, đồng hồ thời gian thực (`#clockTime`) và chuông thông báo.
*   `admin_footer.php`: Đóng thẻ `</main>`, `.main-wrapper` và `</body></html>`.

---

## 🚀 IV. HƯỚNG DẪN CÀI ĐẶT & CHẠY DỰ ÁN TRÊN XAMPP

### 1. Yêu cầu môi trường
*   **XAMPP** (hoặc WampServer, Laragon) hỗ trợ **PHP >= 7.4** hoặc **PHP 8.x**.
*   Trình quản lý cơ sở dữ liệu **phpMyAdmin** / MySQL.
*   Trình duyệt hiện đại (Chrome, Edge, Firefox, Safari).

### 2. Các bước cài đặt

#### Bước 1: Đặt mã nguồn vào thư mục `htdocs`
Đặt toàn bộ thư mục dự án `Restaurant-Management-System` vào thư mục web của XAMPP:
*   **macOS:** `/Applications/XAMPP/xamppfiles/htdocs/Restaurant-Management-System`
*   **Windows:** `C:\xampp\htdocs\Restaurant-Management-System`

#### Bước 2: Khởi động máy chủ
*   Mở **XAMPP Control Panel**.
*   Bật cả hai dịch vụ: **Apache Web Server** và **MySQL Database**.

#### Bước 3: Nhập Cơ sở Dữ liệu (Database)
1. Mở trình duyệt, truy cập: `http://localhost/phpmyadmin/`
2. Tạo một CSDL mới có tên: `restaurant` (chọn bảng mã `utf8mb4_unicode_ci`).
3. Chọn database `restaurant` vừa tạo $\rightarrow$ Nhấn tab **Import (Nhập)**:
   *   Chọn tệp `restaurant.sql` và nhấn **Go** (để tạo cấu trúc bảng và khóa ngoại).
   *   Tiếp tục chọn tệp `restaurant_insert.sql` và nhấn **Go** (để nạp toàn bộ dữ liệu mẫu).

#### Bước 4: Kiểm tra giao diện
Truy cập trực tiếp qua đường dẫn máy chủ cục bộ:
*   **Giao diện Khách hàng:**  
    `http://localhost/Restaurant-Management-System/app/views/client/trangchu.php`
*   **Giao diện Quản trị viên:**  
    `http://localhost/Restaurant-Management-System/app/views/admin/overview.php`

---

## 📋 V. DANH SÁCH BẢNG CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

| Tên bảng | Chức năng nghiệp vụ |
| :--- | :--- |
| `admin` | Tài khoản quản trị viên và nhân viên quản lý |
| `customer` | Tài khoản khách hàng thành viên |
| `category` | Danh mục món ăn (Thịt, Hải sản, Khai vị, Đồ uống,...) |
| `food` | Danh sách món ăn thực đơn, giá bán, giảm giá và ảnh |
| `service_table` | Danh sách bàn ăn, khu vực (Tầng 1, VIP) và sức chứa |
| `reservation` | Phiếu đặt bàn trực tuyến của khách hàng |
| `orders` | Đơn hàng tại bàn hoặc mang về, tổng tiền, trạng thái |
| `order_items` | Chi tiết từng món ăn trong một đơn hàng |
| `ingredient` | Nguyên liệu thực phẩm tồn kho và định mức tối thiểu |
| `inventory_log` | Lịch sử nhập kho / xuất kho nguyên liệu |
| `voucher` | Mã giảm giá ưu đãi khi thanh toán hóa đơn |
| `promotion` | Các bài viết, chiến dịch sự kiện khuyến mãi |

---

## 🎨 VI. QUY CHUẨN THIẾT KẾ & CODE CONVENTIONS

1.  **Quy tắc đặt tên CSS:** Áp dụng phương pháp **BEM (Block__Element--Modifier)**:
    *   *Block:* `sidebar`, `header`, `stat-card`, `data-table`
    *   *Element:* `sidebar__logo`, `sidebar__link`, `stat-card__value`
    *   *Modifier:* `sidebar__link--active`, `btn--primary`, `badge--danger`
2.  **Màu sắc nhận diện:**
    *   Màu chủ đạo (Primary Red): `#a8323d` / `#991b1b` (tông đỏ đô ấm áp, sang trọng của ẩm thực truyền thống và lẩu nướng).
    *   Màu vàng đồng (Gold Accent): `#c9a84c` (dùng làm điểm nhấn trên phân hệ Client).
    *   Màu nền (Background): `#f8f6f4` (sáng ấm) và `#0f0b08` (tối sang trọng cho trang chủ client).
3.  **Tách bạch cấu trúc:**
    *   Không lồng ghép logic nghiệp vụ hay truy vấn SQL vào trong các tệp View.
    *   Các thành phần giao diện lặp lại bắt buộc dùng `include_once` từ thư mục `app/views/layouts/`.
