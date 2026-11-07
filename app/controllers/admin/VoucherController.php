<?php
// app/controllers/admin/VoucherController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/VoucherModel.php';

class VoucherController extends BaseController
{
    public function index()
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
}
