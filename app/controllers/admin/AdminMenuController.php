<?php
// app/controllers/admin/AdminMenuController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/FoodModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';

class AdminMenuController extends BaseController
{
    private function uploadImage(): ?string
    {
        if (!empty($_FILES['photos']['name']) && ($_FILES['photos']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                $uploadDir = __DIR__ . '/../../../public/asset/img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                @chmod($uploadDir, 0777);
                $newFileName = 'img_' . bin2hex(random_bytes(8)) . '.' . $ext;
                if (move_uploaded_file($_FILES['photos']['tmp_name'], $uploadDir . $newFileName)) {
                    return 'public/asset/img/' . $newFileName;
                }
            }
        }
        return null;
    }

    public function index()
    {
        $message     = '';
        $messageType = 'success';
        $foods       = [];
        $categories  = [];
        $fetchedFood = null;
        $method      = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            $foodModel     = new FoodModel();
            $categoryModel = new CategoryModel();

            // 1. Thêm món mới
            if ($method === 'POST' && isset($_POST['btn_insert'])) {
                $name       = trim($_POST['food'] ?? '');
                $categoryId = (int)($_POST['list'] ?? 1);
                $price      = (float)($_POST['price'] ?? 0);
                $discount   = (float)($_POST['discount'] ?? 0);
                $isFeatured = (int)($_POST['is_featured'] ?? 0);
                $desc       = trim($_POST['desc'] ?? '');
                $status     = trim($_POST['status'] ?? 'AVAILABLE');
                $imageURL   = $this->uploadImage() ?? 'public/asset/img/thitbo.png';

                if (!empty($name) && $price >= 0) {
                    $insertedId = $foodModel->create($categoryId, $name, $desc, $price, $discount, $status, $imageURL, $isFeatured);
                    $message     = $insertedId ? "Đã thêm món mới: \"$name\" (Mã: $insertedId) vào cơ sở dữ liệu!" : 'Thêm món thất bại, vui lòng thử lại!';
                    $messageType = $insertedId ? 'success' : 'danger';
                } else {
                    $message     = 'Vui lòng nhập đầy đủ tên món và giá hợp lệ!';
                    $messageType = 'warning';
                }
            }

            // 2. Cập nhật món
            if ($method === 'POST' && isset($_POST['btn_update'])) {
                $id         = (int)($_POST['id'] ?? 0);
                $name       = trim($_POST['food'] ?? '');
                $categoryId = (int)($_POST['list'] ?? 1);
                $price      = (float)($_POST['price'] ?? 0);
                $discount   = (float)($_POST['discount'] ?? 0);
                $isFeatured = (int)($_POST['is_featured'] ?? 0);
                $desc       = trim($_POST['desc'] ?? '');
                $status     = trim($_POST['status'] ?? 'AVAILABLE');
                $imageURL   = $this->uploadImage();

                if ($id <= 0 || empty($name)) {
                    $message     = 'Vui lòng nhập ID hợp lệ và tên món cần cập nhật!';
                    $messageType = 'warning';
                } elseif (!$foodModel->getById($id)) {
                    $message     = "Không tìm thấy món ăn với ID = $id!";
                    $messageType = 'warning';
                } else {
                    $updated     = $foodModel->update($id, $categoryId, $name, $desc, $price, $discount, $status, $imageURL, $isFeatured);
                    $message     = $updated ? "Đã cập nhật thành công món \"$name\" (Mã: $id)!" : 'Cập nhật món thất bại!';
                    $messageType = $updated ? 'success' : 'danger';
                }
            }

            // 3. Xóa món
            if (($method === 'POST' && isset($_POST['btn_delete'])) || (($_GET['action'] ?? '') === 'delete')) {
                $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
                if ($id <= 0) {
                    $message     = 'Vui lòng nhập ID món ăn hợp lệ để xóa!';
                    $messageType = 'warning';
                } elseif (!$foodModel->getById($id)) {
                    $message     = "Không tìm thấy món ăn với ID = $id!";
                    $messageType = 'warning';
                } else {
                    $deleted     = $foodModel->delete($id);
                    $message     = $deleted ? "Đã xóa món ăn có Mã: $id khỏi thực đơn!" : 'Xóa món thất bại!';
                    $messageType = $deleted ? 'success' : 'danger';
                }
            }

            // 4. Lấy thông tin món theo ID
            if ($method === 'POST' && isset($_POST['btn_get'])) {
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    $fetchedFood = $foodModel->getById($id);
                    $message     = $fetchedFood ? "Đã tải thông tin món Mã #$id: \"{$fetchedFood['foodName']}\"" : "Không tìm thấy món ăn với ID = $id";
                    $messageType = $fetchedFood ? 'info' : 'warning';
                } else {
                    $message     = 'Vui lòng nhập ID món ăn cần lấy thông tin!';
                    $messageType = 'warning';
                }
            }

            // 5. Đổi trạng thái Đang bán <-> Ngưng bán
            if (($_GET['action'] ?? '') === 'toggle_status') {
                $id = (int)($_GET['id'] ?? 0);
                if ($id > 0 && ($newSt = $foodModel->toggleStatus($id))) {
                    $stName      = ($newSt === 'AVAILABLE') ? 'Đang bán' : 'Ngưng bán';
                    $message     = "Đã đổi trạng thái món (Mã: $id) sang \"$stName\"!";
                    $messageType = 'info';
                }
            }

            // 6. CRUD Danh mục (Thêm / Cập nhật / Xóa)
            if ($method === 'POST' && isset($_POST['btn_save_category'])) {
                $catId   = (int)($_POST['categoryId'] ?? 0);
                $catName = trim($_POST['categoryName'] ?? '');
                $catDesc = trim($_POST['categoryDesc'] ?? '');
                if (!empty($catName)) {
                    if ($catId > 0) {
                        $categoryModel->update($catId, $catName, $catDesc);
                        $message = "Đã cập nhật danh mục #$catId: \"$catName\"!";
                    } else {
                        $categoryModel->create($catName, $catDesc);
                        $message = "Đã tạo mới danh mục: \"$catName\"!";
                    }
                    $messageType = 'success';
                }
            }

            if (($_GET['action'] ?? '') === 'delete_category') {
                $catId = (int)($_GET['cat_id'] ?? 0);
                if ($catId > 0 && $categoryModel->delete($catId)) {
                    $message     = "Đã xóa danh mục #$catId!";
                    $messageType = 'success';
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
            'fetchedFood' => $fetchedFood,
            'message'     => $message,
            'messageType' => $messageType
        ]);
    }
}
