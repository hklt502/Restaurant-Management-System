<?php
// app/controllers/admin/WarehouseController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/IngredientModel.php';

class WarehouseController extends BaseController
{
    private function saveIngredientPhoto(int $id): ?string
    {
        if ($id <= 0 || empty($_FILES['photos']['name']) || ($_FILES['photos']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        $ext = strtolower(pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return null;
        }
        $uploadDir = __DIR__ . '/../../../public/asset/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        @chmod($uploadDir, 0777);
        foreach (glob($uploadDir . "ingredient_{$id}_*.*") ?: [] as $old) {
            @unlink($old);
        }
        $newFileName = "ingredient_{$id}_" . bin2hex(random_bytes(4)) . ".{$ext}";
        if (move_uploaded_file($_FILES['photos']['tmp_name'], $uploadDir . $newFileName)) {
            return 'public/asset/img/' . $newFileName;
        }
        return null;
    }

    public function index()
    {
        $message     = '';
        $messageType = 'success';
        $ingredients = [];
        $logs        = [];
        $fetchedItem = null;
        $outCount    = 0;
        $lowCount    = 0;
        $method      = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            $ingredientModel = new IngredientModel();
            $adminId         = max(1, (int)($_SESSION['admin']['adminId'] ?? 1));

            // 1. Thêm nguyên liệu mới
            if ($method === 'POST' && isset($_POST['btn_insert'])) {
                $name     = trim($_POST['ingredient'] ?? '');
                $unit     = trim($_POST['unit'] ?? 'kg') ?: 'kg';
                $qty      = max(0, (float)($_POST['quantity'] ?? 0));
                $minStock = max(0, (float)($_POST['min_stock'] ?? 0));

                if ($name !== '') {
                    $newId = $ingredientModel->create($name, $unit, $qty, $minStock, $adminId);
                    if ($newId) {
                        $this->saveIngredientPhoto((int)$newId);
                    }
                    $message     = $newId ? "Đã thêm nguyên liệu \"$name\" (Mã: $newId) vào kho!" : 'Không thể thêm nguyên liệu, vui lòng thử lại!';
                    $messageType = $newId ? 'success' : 'danger';
                } else {
                    $message     = 'Vui lòng nhập tên nguyên liệu!';
                    $messageType = 'warning';
                }
            }

            // 2. Cập nhật nguyên liệu
            if ($method === 'POST' && isset($_POST['btn_update'])) {
                $id   = (int)($_POST['id'] ?? 0);
                $name = trim($_POST['ingredient'] ?? '');
                $unit = trim($_POST['unit'] ?? 'kg') ?: 'kg';
                $existing = $id > 0 ? $ingredientModel->getById($id) : null;

                if ($id <= 0 || $name === '') {
                    $message     = 'Vui lòng nhập ID hợp lệ và tên nguyên liệu cần cập nhật!';
                    $messageType = 'warning';
                } elseif (!$existing) {
                    $message     = "Không tìm thấy nguyên liệu với ID = $id!";
                    $messageType = 'warning';
                } else {
                    $rawQty   = trim((string)($_POST['quantity'] ?? ''));
                    $rawMin   = trim((string)($_POST['min_stock'] ?? ''));
                    $qty      = $rawQty !== '' ? max(0, (float)$rawQty) : (float)$existing['quantityInStock'];
                    $minStock = $rawMin !== '' ? max(0, (float)$rawMin) : (float)$existing['minQuantity'];
                    $ok       = $ingredientModel->update($id, $name, $unit, $qty, $minStock);
                    if ($ok) {
                        $this->saveIngredientPhoto($id);
                    }
                    $message     = $ok ? "Đã cập nhật nguyên liệu \"$name\" (Mã: $id)!" : 'Cập nhật thất bại!';
                    $messageType = $ok ? 'success' : 'danger';
                }
            }

            // 3. Xóa nguyên liệu (POST hoặc GET)
            if (($method === 'POST' && isset($_POST['btn_delete'])) || (($_GET['action'] ?? '') === 'delete')) {
                $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
                if ($id <= 0 || !$ingredientModel->getById($id)) {
                    $message     = 'Vui lòng chọn ID nguyên liệu hợp lệ để xóa!';
                    $messageType = 'warning';
                } else {
                    $ok          = $ingredientModel->delete($id);
                    $message     = $ok ? "Đã xóa nguyên liệu có Mã: $id khỏi kho!" : 'Xóa nguyên liệu thất bại!';
                    $messageType = $ok ? 'success' : 'danger';
                }
            }

            // 4. Lấy thông tin nguyên liệu theo ID
            if ($method === 'POST' && isset($_POST['btn_get'])) {
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    $fetchedItem = $ingredientModel->getById($id);
                    $message     = $fetchedItem ? "Đã tải thông tin nguyên liệu Mã #$id: \"{$fetchedItem['ingredientName']}\"" : "Không tìm thấy nguyên liệu với ID = $id";
                    $messageType = $fetchedItem ? 'info' : 'warning';
                } else {
                    $message     = 'Vui lòng nhập ID nguyên liệu cần lấy thông tin!';
                    $messageType = 'warning';
                }
            }

            // 5. Nhập kho / Xuất kho (ghi vào bảng inventory_log)
            if ($method === 'POST' && isset($_POST['btn_stock_io'])) {
                $ingId   = (int)($_POST['io_ingredient_id'] ?? 0);
                $logType = strtoupper(trim($_POST['io_type'] ?? 'IMPORT')) === 'EXPORT' ? 'EXPORT' : 'IMPORT';
                $ioQty   = (float)($_POST['io_quantity'] ?? 0);
                $ioNote  = trim($_POST['io_note'] ?? '');
                $target  = $ingId > 0 ? $ingredientModel->getById($ingId) : null;

                if (!$target || $ioQty <= 0) {
                    $message     = 'Vui lòng chọn nguyên liệu hợp lệ và nhập số lượng lớn hơn 0!';
                    $messageType = 'warning';
                } elseif ($logType === 'EXPORT' && $ioQty > (float)$target['quantityInStock']) {
                    $avail       = rtrim(rtrim(number_format((float)$target['quantityInStock'], 2, '.', ''), '0'), '.');
                    $message     = "Không thể xuất $ioQty {$target['unit']} \"{$target['ingredientName']}\" vì tồn kho hiện chỉ còn $avail {$target['unit']}!";
                    $messageType = 'warning';
                } else {
                    $ok          = $ingredientModel->adjustStock($ingId, $logType, $ioQty, $ioNote, $adminId);
                    $typeName    = ($logType === 'EXPORT') ? 'Xuất kho' : 'Nhập kho';
                    $message     = $ok ? "$typeName thành công $ioQty {$target['unit']} cho nguyên liệu \"{$target['ingredientName']}\" (#$ingId)!" : 'Thao tác nhập/xuất kho thất bại!';
                    $messageType = $ok ? 'success' : 'danger';
                }
            }

            $ingredients = $ingredientModel->getAll();
            $logs        = $ingredientModel->getLogs(15);

            foreach ($ingredients as $ing) {
                $q = (float)$ing['quantityInStock'];
                $m = (float)$ing['minQuantity'];
                if ($q <= 0) {
                    $outCount++;
                } elseif ($q <= $m) {
                    $lowCount++;
                }
            }
        } catch (Throwable $e) {
        }

        $this->render('admin/warehouse', [
            'message'     => $message,
            'messageType' => $messageType,
            'ingredients' => $ingredients,
            'logs'        => $logs,
            'fetchedItem' => $fetchedItem,
            'outCount'    => $outCount,
            'lowCount'    => $lowCount,
        ]);
    }
}
