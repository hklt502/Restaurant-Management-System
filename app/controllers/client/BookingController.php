<?php
// app/controllers/client/BookingController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/ReservationModel.php';

class BookingController extends BaseController {
    // Hiển thị form đặt bàn
    public function index() {
        $this->render('client/datban', ['error' => null, 'success' => false]);
    }

    // Xử lý form đặt bàn (POST)
    public function store() {
        $name   = trim($_POST['name'] ?? '');
        $phone  = trim($_POST['phone'] ?? '');
        $email  = trim($_POST['email'] ?? '') ?: null;
        $date   = $_POST['date'] ?? '';
        $time   = $_POST['time'] ?? '';
        $guests = (int)($_POST['guests'] ?? 0);
        $branch = $_POST['branch'] ?? null;
        $note   = trim($_POST['note'] ?? '') ?: null;

        if ($name === '' || $phone === '' || $date === '' || $time === '' || $guests < 1) {
            return $this->render('client/datban', ['error' => 'Vui lòng nhập đầy đủ thông tin bắt buộc.', 'success' => false]);
        }
        if ($date < date('Y-m-d')) {
            return $this->render('client/datban', ['error' => 'Ngày đặt bàn không hợp lệ.', 'success' => false]);
        }

        $id = (new ReservationModel())->create($this->customerId(), $name, $phone, $email, $date, $time, $guests, $branch, $note);

        $this->render('client/datban', [
            'error'   => $id ? null : 'Đặt bàn thất bại, vui lòng thử lại.',
            'success' => (bool)$id,
        ]);
    }

    // Khách hủy đặt bàn của mình
    public function cancel() {
        $this->requireLogin();
        (new ReservationModel())->cancel((int)($_POST['reservationId'] ?? 0), $this->customerId());
        $this->redirect('index.php?page=canhan');
    }
}

