<?php
// app/controllers/client/PromotionController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/PromotionModel.php';
require_once __DIR__ . '/../../models/VoucherModel.php';

class PromotionController extends BaseController {
    
    // Hiển thị danh sách chương trình khuyến mãi và các voucher đính kèm
    public function index() {
        $promotionModel = new PromotionModel();
        $voucherModel   = new VoucherModel();

        $promotions = $promotionModel->getAllActive();

        // Gắn danh sách voucher vào từng promotion
        foreach ($promotions as &$promo) {
            $promo['vouchers'] = $voucherModel->getByPromotion($promo['promotionId']);
        }
        unset($promo);

        // Lấy danh sách ID các voucher mà khách hàng này đã nhận trong ví (nếu đã đăng nhập)
        $claimedVoucherIds = [];
        $customerId = $this->customerId();
        if ($customerId) {
            $myVouchers = $voucherModel->getByCustomer($customerId);
            foreach ($myVouchers as $mv) {
                if ($mv['status'] === 'UNUSED') {
                    $claimedVoucherIds[] = (int)$mv['voucherId'];
                }
            }
        }

        $message = $_SESSION['promo_message'] ?? null;
        $error   = $_SESSION['promo_error'] ?? null;
        unset($_SESSION['promo_message'], $_SESSION['promo_error']);

        $this->render('client/khuyenmai', [
            'promotions'        => $promotions,
            'claimedVoucherIds' => $claimedVoucherIds,
            'message'           => $message,
            'error'             => $error
        ]);
    }

    // Nhận voucher từ cửa sổ chi tiết của promotion và lưu vào ví customer
    public function claimVoucher() {
        $customerId = $this->customerId();
        if (!$customerId) {
            // Chưa đăng nhập -> Chuyển hướng đến trang đăng nhập
            $this->redirect('index.php?page=dangnhap');
            return;
        }

        $voucherId = (int)($_POST['voucherId'] ?? 0);
        if ($voucherId <= 0) {
            $this->redirect('index.php?page=khuyenmai');
            return;
        }

        $voucherModel = new VoucherModel();
        $voucher = $voucherModel->getById($voucherId);

        if (!$voucher || !$voucher['isActive']) {
            $_SESSION['promo_error'] = 'Voucher không tồn tại hoặc đã hết hiệu lực.';
            $this->redirect('index.php?page=khuyenmai');
            return;
        }

        // Kiểm tra xem khách đã lưu mã này trong ví mà chưa dùng không
        if ($voucherModel->hasCustomerVoucher($customerId, $voucherId)) {
            $_SESSION['promo_error'] = 'Bạn đã lưu mã voucher ' . htmlspecialchars($voucher['voucherCode']) . ' trong ví rồi!';
            $this->redirect('index.php?page=khuyenmai');
            return;
        }

        // Gọi hàm assignToCustomer để thêm voucher vào ví customer
        $success = $voucherModel->assignToCustomer($customerId, $voucherId);

        if ($success) {
            $_SESSION['promo_message'] = '🎉 Đã nhận mã ưu đãi ' . htmlspecialchars($voucher['voucherCode']) . ' thành công! Mã đã được thêm vào Ví Voucher của bạn.';
        } else {
            $_SESSION['promo_error'] = 'Không thể nhận voucher lúc này, vui lòng thử lại sau.';
        }

        $this->redirect('index.php?page=khuyenmai');
    }
}
