<?php
// app/controllers/admin/TableController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/TableModel.php';
require_once __DIR__ . '/../../models/ReservationModel.php';

class TableController extends BaseController
{
    public function index()
    {
        $this->requireAdmin();

        $tableModel = new TableModel();
        $reservationModel = new ReservationModel();

        // Xử lý các request AJAX thao tác bàn ăn & lịch hẹn
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            header('Content-Type: application/json; charset=utf-8');
            $action = $_POST['action'];

            try {
                if ($action === 'update_status') {
                    $this->handleAjaxUpdateStatus($tableModel);
                } elseif ($action === 'save_table') {
                    $this->handleAjaxSaveTable($tableModel);
                } elseif ($action === 'delete_table') {
                    $this->handleAjaxDeleteTable($tableModel);
                } elseif ($action === 'cancel_reservation') {
                    $this->handleAjaxCancelReservation($reservationModel);
                } elseif ($action === 'confirm_reservation') {
                    $this->handleAjaxConfirmReservation($reservationModel);
                } else {
                    throw new Exception('Yêu cầu không hợp lệ.');
                }
            } catch (Throwable $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        $tables = [];
        try {
            $tables = $tableModel->getAll();
        } catch (Throwable $e) {
            $tables = [];
        }

        $reservations = [];
        try {
            $reservations = $reservationModel->getAllAdmin();
        } catch (Throwable $e) {
            $reservations = [];
        }

        $this->render('admin/tables', [
            'tables' => $tables,
            'reservations' => $reservations
        ]);
    }

    private function handleAjaxUpdateStatus(TableModel $tableModel): void
    {
        $id     = (int)($_POST['id'] ?? 0);
        $status = strtoupper(trim($_POST['status'] ?? ''));

        if (!in_array($status, ['AVAILABLE', 'OCCUPIED', 'RESERVED'], true)) {
            throw new Exception('Trạng thái không hợp lệ.');
        }

        if (!$tableModel->getById($id)) {
            throw new Exception('Không tìm thấy bàn.');
        }

        $ok = $tableModel->updateStatus($id, $status);
        if (!$ok) {
            throw new Exception('Không thể cập nhật trạng thái bàn.');
        }

        echo json_encode(['success' => true]);
        exit;
    }

    private function handleAjaxSaveTable(TableModel $tableModel): void
    {
        $id       = (int)($_POST['id'] ?? 0);
        $number   = trim($_POST['tableNumber'] ?? '');
        $area     = trim($_POST['area'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);
        $status   = strtoupper(trim($_POST['status'] ?? 'AVAILABLE'));

        if ($number === '' || $area === '' || $capacity < 1) {
            throw new Exception('Vui lòng nhập đầy đủ thông tin bàn.');
        }

        if (!in_array($status, ['AVAILABLE', 'OCCUPIED', 'RESERVED'], true)) {
            throw new Exception('Trạng thái không hợp lệ.');
        }

        $ok = $tableModel->saveTable($id, $number, $area, $capacity, $status);
        if (!$ok) {
            throw new Exception('Không thể lưu thông tin bàn.');
        }

        echo json_encode(['success' => true]);
        exit;
    }

    private function handleAjaxDeleteTable(TableModel $tableModel): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0 || !$tableModel->getById($id)) {
            throw new Exception('Không tìm thấy bàn để xóa.');
        }

        $ok = $tableModel->delete($id);
        if (!$ok) {
            throw new Exception('Xóa bàn thất bại.');
        }

        echo json_encode(['success' => true]);
        exit;
    }

    private function handleAjaxCancelReservation(ReservationModel $reservationModel): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception('Mã đặt bàn không hợp lệ.');
        }

        $res = $reservationModel->cancelByAdmin($id);
        if (!$res || empty($res['success'])) {
            throw new Exception('Không thể hủy lịch hẹn hoặc lịch hẹn không tồn tại.');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Đã hủy lịch hẹn thành công.',
            'tableId' => $res['tableId'] ?? null,
            'tableStatus' => $res['tableStatus'] ?? null
        ]);
        exit;
    }

    private function handleAjaxConfirmReservation(ReservationModel $reservationModel): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception('Mã đặt bàn không hợp lệ.');
        }

        $res = $reservationModel->confirmByAdmin($id);
        if (!$res || empty($res['success'])) {
            throw new Exception('Không thể xác nhận lịch hẹn (lịch hẹn không tồn tại hoặc đã bị hủy).');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Đã xác nhận lịch hẹn thành công.',
            'tableId' => $res['tableId'] ?? null,
            'tableStatus' => $res['tableStatus'] ?? null
        ]);
        exit;
    }
}
