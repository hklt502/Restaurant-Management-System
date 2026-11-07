<?php
// app/controllers/admin/UserController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/AdminModel.php';
require_once __DIR__ . '/../../models/CustomerModel.php';

class UserController extends BaseController
{
    public function index()
    {
        $message = '';
        $messageType = 'success';
        $allUsers = [];

        try {
            $adminModel    = new AdminModel();
            $customerModel = new CustomerModel();

            // 1. Thêm người dùng mới (Vai trò theo tên bảng: Quản trị và Khách hàng)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_add_user'])) {
                $fullname = trim($_POST['fullname'] ?? '');
                $username = trim($_POST['username'] ?? '');
                $email    = trim($_POST['email'] ?? '');
                $phone    = trim($_POST['phone'] ?? '');
                $role     = trim($_POST['role'] ?? 'Khách hàng');
                $password = trim($_POST['password'] ?? '');
                $status   = isset($_POST['status']) ? 0 : 1; // 0: Hoạt động, 1: Bị khóa

                if (empty($fullname) || empty($password)) {
                    $message = "Vui lòng nhập đầy đủ họ tên và mật khẩu!";
                    $messageType = "danger";
                } else {
                    if ($role === 'Quản trị' || $role === 'admin') {
                        if (empty($username)) {
                            $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $phone ?: 'admin' . time()));
                        }
                        if ($adminModel->findByUsername($username)) {
                            $message = "Tên đăng nhập \"$username\" đã tồn tại!";
                            $messageType = "danger";
                        } else {
                            $created = $adminModel->create($username, $password, $fullname, $phone);
                            if ($created) {
                                $newAdmin = $adminModel->findByUsername($username);
                                if ($status === 1 && $newAdmin) {
                                    $adminModel->toggleStatus($newAdmin['adminId']);
                                }
                                $message = "Đã thêm tài khoản Quản trị: \"$fullname\" thành công!";
                                $messageType = "success";
                            } else {
                                $message = "Thêm tài khoản Quản trị thất bại!";
                                $messageType = "danger";
                            }
                        }
                    } else {
                        if (empty($phone)) {
                            $message = "Khách hàng bắt buộc phải có số điện thoại!";
                            $messageType = "danger";
                        } elseif ($customerModel->findByPhone($phone)) {
                            $message = "Số điện thoại $phone đã được sử dụng!";
                            $messageType = "danger";
                        } else {
                            $newId = $customerModel->create($fullname, $phone, $email ?: null, $password, null, 'MALE');
                            if ($newId) {
                                if ($status === 1) {
                                    $customerModel->toggleStatus($newId);
                                }
                                $message = "Đã thêm thành công Khách hàng: \"$fullname\"!";
                                $messageType = "success";
                            } else {
                                $message = "Thêm Khách hàng thất bại!";
                                $messageType = "danger";
                            }
                        }
                    }
                }
            }

            // 2. Chỉnh sửa thông tin người dùng
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_edit_user'])) {
                $id       = (int)($_POST['userId'] ?? 0);
                $type     = trim($_POST['userType'] ?? 'customer');
                $fullname = trim($_POST['fullname'] ?? '');
                $phone    = trim($_POST['phone'] ?? '');
                $email    = trim($_POST['email'] ?? '');
                $username = trim($_POST['username'] ?? '');

                if ($id > 0 && !empty($fullname)) {
                    if ($type === 'admin') {
                        $ok = $adminModel->update($id, $username, $fullname, $phone);
                    } else {
                        $ok = $customerModel->update($id, $fullname, $phone, $email);
                    }
                    if ($ok) {
                        $message = "Cập nhật thông tin thành công!";
                        $messageType = "success";
                    } else {
                        $message = "Cập nhật thông tin thất bại!";
                        $messageType = "danger";
                    }
                }
            }

            // 3. Khóa / Mở khóa tài khoản
            if (isset($_GET['action']) && $_GET['action'] === 'toggle') {
                $id   = (int)($_GET['id'] ?? 0);
                $type = trim($_GET['type'] ?? 'customer');
                if ($id > 0) {
                    if ($type === 'admin') {
                        $adminModel->toggleStatus($id);
                    } else {
                        $customerModel->toggleStatus($id);
                    }
                    $message = "Đã cập nhật trạng thái tài khoản!";
                    $messageType = "info";
                }
            }

            // 4. Xóa mềm người dùng
            if (isset($_GET['action']) && $_GET['action'] === 'delete') {
                $id   = (int)($_GET['id'] ?? 0);
                $type = trim($_GET['type'] ?? 'customer');
                if ($id > 0) {
                    if ($type === 'admin') {
                        $adminModel->delete($id);
                    } else {
                        $customerModel->delete($id);
                    }
                    $message = "Đã xóa người dùng khỏi hệ thống!";
                    $messageType = "success";
                }
            }

            // 5. Đổ data từ 2 bảng quản trị và khách hàng
            foreach ($adminModel->getAll(false) as $a) {
                $allUsers[] = [
                    'type'      => 'admin',
                    'id'        => (int)$a['adminId'],
                    'code'      => '#ADM' . str_pad($a['adminId'], 3, '0', STR_PAD_LEFT),
                    'name'      => $a['adminName'] ?: 'Quản trị viên',
                    'email'     => $a['username'] ?? '',
                    'phone'     => $a['adminPhone'] ?: 'Chưa có SĐT',
                    'role'      => 'Quản trị',
                    'status'    => !empty($a['isDeleted']) ? 'locked' : 'active',
                    'createdAt' => !empty($a['createdAt']) ? date('d/m/Y', strtotime($a['createdAt'])) : '24/10/2026'
                ];
            }
            foreach ($customerModel->getAll(false) as $c) {
                $allUsers[] = [
                    'type'      => 'customer',
                    'id'        => (int)$c['customerId'],
                    'code'      => '#CUS' . str_pad($c['customerId'], 3, '0', STR_PAD_LEFT),
                    'name'      => $c['customerName'] ?: 'Khách hàng',
                    'email'     => !empty($c['customerEmail']) ? $c['customerEmail'] : 'Chưa cập nhật',
                    'phone'     => $c['customerPhone'] ?: 'Chưa có SĐT',
                    'role'      => 'Khách hàng',
                    'status'    => !empty($c['isDeleted']) ? 'locked' : 'active',
                    'createdAt' => !empty($c['createdAt']) ? date('d/m/Y', strtotime($c['createdAt'])) : '24/10/2026'
                ];
            }
        } catch (Throwable $e) {
        }

        $statTotal    = count($allUsers);
        $statActive   = 0;
        $statLocked   = 0;
        $statNewMonth = 0;
        $currentMonth = date('m/Y');

        foreach ($allUsers as $u) {
            if ($u['status'] === 'active') $statActive++;
            else $statLocked++;
            if (substr($u['createdAt'], 3) === $currentMonth) $statNewMonth++;
        }

        // 6. Tìm kiếm & Lọc theo Vai trò ("Quản trị", "Khách hàng") và Trạng thái
        $searchKeyword = trim($_GET['search'] ?? '');
        $filterRole    = trim($_GET['role'] ?? '');
        $filterStatus  = trim($_GET['status'] ?? '');

        $filteredUsers = array_values(array_filter($allUsers, function ($u) use ($searchKeyword, $filterRole, $filterStatus) {
            if ($filterRole !== '' && $u['role'] !== $filterRole) return false;
            if ($filterStatus !== '' && $u['status'] !== $filterStatus) return false;
            if ($searchKeyword !== '') {
                $haystack = $u['name'] . ' ' . $u['email'] . ' ' . $u['phone'] . ' ' . $u['code'];
                if (mb_stripos($haystack, $searchKeyword, 0, 'UTF-8') === false) return false;
            }
            return true;
        }));

        // 7. PHÂN TRANG - CHỈ CHO NEXT / PREV
        $perPage     = 6;
        $totalUsers  = count($filteredUsers);
        $totalPages  = max(1, (int)ceil($totalUsers / $perPage));
        $currentPage = min($totalPages, max(1, (int)($_GET['p'] ?? 1)));
        $offset      = ($currentPage - 1) * $perPage;

        $queryParams = array_filter([
            'search' => $searchKeyword,
            'role'   => $filterRole,
            'status' => $filterStatus
        ], fn($v) => $v !== '');

        $buildPageUrl = fn($p) => 'index.php?' . http_build_query(array_merge(['page' => 'admin-users', 'p' => $p], $queryParams));

        $this->render('admin/users', [
            'users'         => array_slice($filteredUsers, $offset, $perPage),
            'statTotal'     => $statTotal,
            'statActive'    => $statActive,
            'statLocked'    => $statLocked,
            'statNewMonth'  => $statNewMonth,
            'currentPage'   => $currentPage,
            'totalPages'    => $totalPages,
            'startItem'     => $totalUsers > 0 ? $offset + 1 : 0,
            'endItem'       => min($offset + $perPage, $totalUsers),
            'totalUsers'    => $totalUsers,
            'prevPageUrl'   => ($currentPage > 1) ? $buildPageUrl($currentPage - 1) : null,
            'nextPageUrl'   => ($currentPage < $totalPages) ? $buildPageUrl($currentPage + 1) : null,
            'searchKeyword' => $searchKeyword,
            'filterRole'    => $filterRole,
            'filterStatus'  => $filterStatus,
            'message'       => $message,
            'messageType'   => $messageType
        ]);
    }
}
