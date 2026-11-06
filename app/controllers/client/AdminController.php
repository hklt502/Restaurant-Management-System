<?php
// app/controllers/client/AdminController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/AdminModel.php';
require_once __DIR__ . '/../../models/CustomerModel.php';
require_once __DIR__ . '/../../models/FoodModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/StatisticModel.php';
require_once __DIR__ . '/../../models/TableModel.php';
require_once __DIR__ . '/../../models/VoucherModel.php';

class AdminController extends BaseController
{
    /**
     * Đăng nhập Quản trị viên
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $adminModel = new AdminModel();
            $admin = $adminModel->login($username, $password);

            if ($admin) {
                $_SESSION['admin'] = $admin;
                $this->redirect('index.php?page=admin-overview');
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng";
                $this->render('admin/login', ['error' => $error]);
            }
        } else {
            $this->render('admin/login');
        }
    }

    /**
     * Trang Tổng quan (Dashboard Overview)
     */
    public function overview()
    {
        $this->render('admin/overview');
    }

    /**
     * Trang Quản lý Thực đơn (Menu CRUD với FoodModel & CategoryModel)
     */
    public function menu()
    {
        $message = '';
        $messageType = 'success';
        $foods = [];
        $categories = [];

        try {
            $foodModel = new FoodModel();
            $categoryModel = new CategoryModel();

            // 1. Thêm món mới
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_insert'])) {
                $name       = trim($_POST['food'] ?? '');
                $categoryId = (int)($_POST['list'] ?? 1);
                $price      = (float)($_POST['price'] ?? 0);
                $desc       = trim($_POST['desc'] ?? '');
                $status     = trim($_POST['status'] ?? 'AVAILABLE');
                $imageURL   = '/public/asset/img/thitbo.png';

                if (!empty($_FILES['photos']['name']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $newFileName = 'dish_' . time() . '.' . $ext;
                        $uploadDir = __DIR__ . '/../../../public/asset/img/';
                        if (move_uploaded_file($_FILES['photos']['tmp_name'], $uploadDir . $newFileName)) {
                            $imageURL = '/public/asset/img/' . $newFileName;
                        }
                    }
                }

                if (!empty($name) && $price >= 0) {
                    $insertedId = $foodModel->create($categoryId, $name, $desc, $price, 0, $status, $imageURL, 0);
                    if ($insertedId) {
                        $message = "Đã thêm món mới: \"$name\" (Mã: $insertedId) vào cơ sở dữ liệu!";
                        $messageType = "success";
                    } else {
                        $message = "Thêm món thất bại, vui lòng thử lại!";
                        $messageType = "danger";
                    }
                } else {
                    $message = "Vui lòng nhập đầy đủ tên món và giá hợp lệ!";
                    $messageType = "warning";
                }
            }

            // 2. Cập nhật món
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_update'])) {
                $id         = (int)($_POST['id'] ?? 0);
                $name       = trim($_POST['food'] ?? '');
                $categoryId = (int)($_POST['list'] ?? 1);
                $price      = (float)($_POST['price'] ?? 0);
                $desc       = trim($_POST['desc'] ?? '');
                $status     = trim($_POST['status'] ?? 'AVAILABLE');
                $imageURL   = null;

                if (!empty($_FILES['photos']['name']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $newFileName = 'dish_' . time() . '.' . $ext;
                        $uploadDir = __DIR__ . '/../../../public/asset/img/';
                        if (move_uploaded_file($_FILES['photos']['tmp_name'], $uploadDir . $newFileName)) {
                            $imageURL = '/public/asset/img/' . $newFileName;
                        }
                    }
                }

                if ($id > 0 && !empty($name)) {
                    $updated = $foodModel->update($id, $categoryId, $name, $desc, $price, 0, $status, $imageURL, 0);
                    if ($updated) {
                        $message = "Đã cập nhật thành công món \"$name\" (Mã: $id)!";
                        $messageType = "success";
                    } else {
                        $message = "Cập nhật món thất bại!";
                        $messageType = "danger";
                    }
                } else {
                    $message = "Vui lòng nhập ID hợp lệ và tên món cần cập nhật!";
                    $messageType = "warning";
                }
            }

            // 3. Xóa món
            if (($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_delete'])) || (isset($_GET['action']) && $_GET['action'] === 'delete')) {
                $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
                if ($id > 0) {
                    $deleted = $foodModel->delete($id);
                    if ($deleted) {
                        $message = "Đã xóa món ăn có Mã: $id khỏi thực đơn!";
                        $messageType = "success";
                    } else {
                        $message = "Xóa món thất bại!";
                        $messageType = "danger";
                    }
                }
            }

            // 4. Đổi trạng thái Đang bán <-> Ngưng bán
            if (isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
                $id = (int)($_GET['id'] ?? 0);
                if ($id > 0) {
                    $newSt = $foodModel->toggleStatus($id);
                    if ($newSt) {
                        $stName = ($newSt === 'AVAILABLE') ? 'Đang bán' : 'Ngưng bán';
                        $message = "Đã đổi trạng thái món (Mã: $id) sang \"$stName\"!";
                        $messageType = "info";
                    }
                }
            }

            // 5. Tạo danh mục mới
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save_category'])) {
                $catName = trim($_POST['categoryName'] ?? '');
                $catDesc = trim($_POST['categoryDesc'] ?? '');
                if (!empty($catName)) {
                    $catId = $categoryModel->create($catName, $catDesc);
                    if ($catId) {
                        $message = "Đã tạo mới danh mục: \"$catName\"!";
                        $messageType = "success";
                    }
                }
            }

            $categories = $categoryModel->getAll();
            $foods      = $foodModel->getAllAdmin();
        } catch (Throwable $e) {
            $categories = [];
            $foods      = [];
        }

        $this->render('admin/menu', [
            'foods'       => $foods,
            'categories'  => $categories,
            'message'     => $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * Trang Quản lý Đơn hàng (Orders với OrderModel & FoodModel)
     */
    public function orders()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            header('Content-Type: application/json; charset=utf-8');

            // 1. Xử lý AJAX cập nhật trạng thái đơn hàng xuống Database
            if ($_POST['action'] === 'update_status') {
                $rawId     = $_POST['orderId'] ?? '';
                $newStatus = $_POST['status'] ?? '';
                $numId     = (int) preg_replace('/\D/', '', $rawId);
                $ok = false;
                try {
                    $orderModel = new OrderModel();
                    if ($numId > 0) {
                        $ok = $orderModel->updateStatus($numId, $newStatus);
                    }
                } catch (Throwable $e) {
                    $ok = false;
                }
                echo json_encode(['success' => $ok, 'orderId' => $rawId, 'status' => $newStatus]);
                exit;
            }

            // 2. Xử lý AJAX tạo đơn hàng mới từ POS và INSERT vào Database
            if ($_POST['action'] === 'create_order') {
                try {
                    $orderModel = new OrderModel();
                    $newOrder   = $orderModel->createOrderFromPos($_POST);
                    echo json_encode(['success' => true, 'order' => $newOrder], JSON_UNESCAPED_UNICODE);
                } catch (Throwable $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                }
                exit;
            }
        }

        $dbOrders   = [];
        $dbFoodsPos = [];
        try {
            $orderModel = new OrderModel();
            $dbOrders   = $orderModel->getAllOrders(100);

            $foodModel = new FoodModel();
            $rawFoods  = $foodModel->getAllAdmin();
            foreach ($rawFoods as $f) {
                $catId  = (int)($f['categoryId'] ?? 1);
                $catKey = 'beef';
                if ($catId === 2) $catKey = 'seafood';
                elseif ($catId === 3 || $catId === 4) $catKey = 'appetizer';
                elseif ($catId === 5) $catKey = 'drink';
                $dbFoodsPos[] = [
                    'id'    => (int)$f['foodId'],
                    'name'  => $f['foodName'] ?? $f['name'] ?? 'Món ăn',
                    'cat'   => $catKey,
                    'price' => (float)($f['foodPrice'] ?? $f['price'] ?? 0),
                    'unit'  => 'Phần'
                ];
            }
        } catch (Throwable $e) {
            $dbOrders   = [];
            $dbFoodsPos = [];
        }

        $this->render('admin/orders', [
            'dbOrders'   => $dbOrders,
            'dbFoodsPos' => $dbFoodsPos
        ]);
    }

    /**
     * Trang Thống kê & Báo cáo (Statistics với StatisticModel)
     */
    public function statistics()
    {
        $dbStatistics = null;
        try {
            $statModel   = new StatisticModel();
            $allSummary  = $statModel->getSummary('2000-01-01 00:00:00', '2099-12-31 23:59:59');
            $allStatus   = $statModel->getOrderStatus('2000-01-01 00:00:00', '2099-12-31 23:59:59');
            $topFoods    = $statModel->getTopFoods('2000-01-01 00:00:00', '2099-12-31 23:59:59', 4);
            $customerSrc = $statModel->getCustomerSource('2000-01-01 00:00:00', '2099-12-31 23:59:59');

            $monthStart  = date('Y-m-01 00:00:00');
            $monthEnd    = date('Y-m-t 23:59:59');
            $monthRevDay = $statModel->getRevenueByPeriod($monthStart, $monthEnd, 'day');

            if (!empty($allSummary['totalOrders']) && $allSummary['totalOrders'] > 0) {
                $dbStatistics = [
                    'summary'     => $allSummary,
                    'status'      => $allStatus,
                    'topFoods'    => $topFoods,
                    'source'      => $customerSrc,
                    'monthRevDay' => $monthRevDay
                ];
            }
        } catch (Throwable $e) {
            $dbStatistics = null;
        }

        $this->render('admin/statistics', [
            'dbStatistics' => $dbStatistics
        ]);
    }

    /**
     * Trang Quản lý Người dùng
     */
    public function users()
    {
        $admins    = [];
        $customers = [];
        try {
            $adminModel    = new AdminModel();
            $customerModel = new CustomerModel();
            $admins        = $adminModel->getAll();
            $customers     = $customerModel->getAll();
        } catch (Throwable $e) {
            $admins    = [];
            $customers = [];
        }

        $this->render('admin/users', [
            'admins'    => $admins,
            'customers' => $customers
        ]);
    }

    /**
     * Trang Quản lý Bàn
     */
    public function tables()
    {
        $tables = [];
        try {
            $tableModel = new TableModel();
            $tables     = $tableModel->getAll();
        } catch (Throwable $e) {
            $tables = [];
        }

        $this->render('admin/tables', [
            'tables' => $tables
        ]);
    }

    /**
     * Trang Quản lý Voucher & Khuyến mãi (Kết nối VoucherModel)
     */
    public function vouchers()
    {
        $message     = '';
        $messageType = 'success';
        $vouchers    = [];
        $promotions  = [];

        try {
            $voucherModel = new VoucherModel();

            // 1. Thêm mới hoặc Cập nhật Voucher
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save_voucher'])) {
                $voucherId     = (int)($_POST['voucherId'] ?? 0);
                $code          = strtoupper(trim($_POST['voucherCode'] ?? ''));
                $discountType  = strtoupper(trim($_POST['discountType'] ?? 'PERCENT'));
                $discountValue = (float)($_POST['discountValue'] ?? 0);
                $minOrderValue = (float)($_POST['minOrderValue'] ?? 0);
                $expiryDate    = !empty($_POST['expiryDate']) ? $_POST['expiryDate'] : null;
                $usageLimit    = max(1, (int)($_POST['usageLimit'] ?? 100));
                $promotionId   = !empty($_POST['promotionId']) ? (int)$_POST['promotionId'] : null;
                $isActive      = isset($_POST['isActive']) ? 1 : 0;

                if (!empty($code) && $discountValue > 0) {
                    if ($voucherId > 0) {
                        $ok = $voucherModel->update($voucherId, $code, $discountType, $discountValue, $minOrderValue, $expiryDate, $usageLimit, $promotionId, $isActive);
                        $message     = $ok ? "Đã cập nhật thành công Voucher \"$code\"!" : "Không thể cập nhật Voucher \"$code\"!";
                        $messageType = $ok ? 'success' : 'danger';
                    } else {
                        $newId = $voucherModel->create($code, $discountType, $discountValue, $minOrderValue, $expiryDate, $usageLimit, $promotionId, $isActive);
                        $message     = $newId ? "Đã tạo mới thành công Voucher \"$code\" (ID: $newId) vào Database!" : "Mã Voucher \"$code\" đã tồn tại hoặc dữ liệu không hợp lệ!";
                        $messageType = $newId ? 'success' : 'danger';
                    }
                } else {
                    $message     = 'Vui lòng nhập đầy đủ Mã Voucher và Mức giảm lớn hơn 0!';
                    $messageType = 'danger';
                }
            }

            // 2. Tạm ngưng / Kích hoạt lại Voucher
            if (isset($_GET['action']) && $_GET['action'] === 'toggle') {
                $id = (int)($_GET['id'] ?? 0);
                if ($id > 0) {
                    $newSt = $voucherModel->toggleActive($id);
                    if ($newSt !== false) {
                        $stLabel     = ($newSt === 1) ? 'Đang hoạt động' : 'Tạm ngưng';
                        $message     = "Đã chuyển trạng thái Voucher #$id sang \"$stLabel\"!";
                        $messageType = 'info';
                    }
                }
            }

            // 3. Xóa mềm Voucher
            if (isset($_GET['action']) && $_GET['action'] === 'delete') {
                $id = (int)($_GET['id'] ?? 0);
                if ($id > 0 && $voucherModel->delete($id)) {
                    $message     = "Đã xóa Voucher #$id khỏi danh sách!";
                    $messageType = 'success';
                }
            }

            $vouchers   = $voucherModel->getAll();
            $promotions = $voucherModel->getPromotions();
        } catch (Throwable $e) {
            $vouchers   = [];
            $promotions = [];
        }

        $this->render('admin/vouchers', [
            'vouchers'    => $vouchers,
            'promotions'  => $promotions,
            'message'     => $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * Trang Quản lý Kho
     */
    public function warehouse()
    {
        $this->render('admin/warehouse');
    }
}
