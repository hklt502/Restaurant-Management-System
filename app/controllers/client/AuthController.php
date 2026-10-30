<?php
// app/controllers/client/AuthController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/CustomerModel.php';
require_once __DIR__ . '/../../models/ReservationModel.php';
require_once __DIR__ . '/../../models/VoucherModel.php';

class AuthController extends BaseController
{
    private $customers;

    public function __construct()
    {
        parent::__construct();
        $this->customers = new CustomerModel();
    }

    // ---------- Đăng nhập ----------
    public function loginForm()
    {
        $this->render('client/dangnhap', ['error' => null]);
    }

    public function login()
    {
        $phone    = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        $customer = $this->customers->login($phone, $password);
        if (!$customer) {
            return $this->render('client/dangnhap', ['error' => 'Số điện thoại hoặc mật khẩu không đúng.']);
        }

        session_regenerate_id(true);
        $_SESSION['customer'] = [
            'customerId'   => $customer['customerId'],
            'customerName' => $customer['customerName'],
            'customerPhone' => $customer['customerPhone'],
            'customerEmail' => $customer['customerEmail']
        ];
        $this->redirect('index.php?page=trangchu');
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        $this->redirect('index.php?page=dangnhap');
    }

    // ---------- Đăng ký ----------
    public function registerForm()
    {
        $this->render('client/dangky', ['error' => null]);
    }

    public function register()
    {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $birth    = $_POST['birth'] ?? null;
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        // Form gửi nam/nu/khac -> map sang ENUM của DB
        $gender   = ['nam' => 'MALE', 'nu' => 'FEMALE'][$_POST['gender'] ?? ''] ?? 'OTHER';

        $error = null;
        if ($name === '' || $phone === '' || $email === '' || $password === '') {
            $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
        } elseif ($password !== $confirm) {
            $error = 'Mật khẩu xác nhận không khớp.';
        } elseif ($this->customers->findByPhone($phone)) {
            $error = 'Số điện thoại đã được đăng ký.';
        } elseif ($this->customers->emailExists($email)) {
            $error = 'Email đã được đăng ký.';
        }
        if ($error) {
            return $this->render('client/dangky', ['error' => $error]);
        }

        $id = $this->customers->create($name, $phone, $email, $password, $birth, $gender);
        if (!$id) {
            return $this->render('client/dangky', ['error' => 'Đăng ký thất bại, vui lòng thử lại.']);
        }
        $this->redirect('index.php?page=dangnhap');
    }

    // ---------- Trang cá nhân ----------
    public function profile()
    {
        $this->requireLogin();
        $this->render('client/canhan', [
            'customer'     => $this->customers->getById($this->customerId()),
            'reservations' => (new ReservationModel())->getByCustomer($this->customerId()),
            'vouchers'     => (new VoucherModel())->getByCustomer($this->customerId()),
        ]);
    }

    // ---------- Đổi mật khẩu ----------
    public function changePasswordForm()
    {
        $this->requireLogin();
        $this->render('client/doimatkhau', ['error' => null, 'success' => false]);
    }

    public function changePassword()
    {
        $this->requireLogin();
        $old     = $_POST['old-pass'] ?? '';
        $new     = $_POST['new-pass'] ?? '';
        $confirm = $_POST['confirm-pass'] ?? '';

        $error = null;
        if ($new === '' || $new !== $confirm) {
            $error = 'Mật khẩu mới không hợp lệ hoặc không khớp.';
        } elseif (!$this->customers->changePassword($this->customerId(), $old, $new)) {
            $error = 'Mật khẩu cũ không đúng.';
        }

        $this->render('client/doimatkhau', ['error' => $error, 'success' => $error === null]);
    }
}
