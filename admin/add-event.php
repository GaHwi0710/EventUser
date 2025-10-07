<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/Event.php';

if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect(SITE_URL . '/auth/login.php');
    exit;
}

 $database = new Database();
 $db = $database->getConnection();
 $event = new Event($db);

 $message = '';
 $errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event->title = trim($_POST['title']);
    $event->description = trim($_POST['description']);
    $event->date = $_POST['date'];
    $event->time = $_POST['time'];
    $event->location = trim($_POST['location']);
    $event->status = $_POST['status'];
    
    $event->image = null;
    
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/events/"; 
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                $errors[] = "Không thể tạo thư mục lưu trữ hình ảnh";
            }
        }
        
        if (is_dir($targetDir) && !is_writable($targetDir)) {
            $errors[] = "Thư mục lưu trữ không có quyền ghi";
        }
        
        if (empty($errors)) {
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File quá lớn (vượt quá giới hạn server)',
                    UPLOAD_ERR_FORM_SIZE => 'File quá lớn (vượt quá giới hạn form)',
                    UPLOAD_ERR_PARTIAL => 'File chỉ được tải lên một phần',
                    UPLOAD_ERR_NO_FILE => 'Không có file được tải lên',
                    UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
                    UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file vào disk',
                    UPLOAD_ERR_EXTENSION => 'Tải lên bị dừng bởi extension'
                ];
                $errors[] = $errorMessages[$_FILES['image']['error']] ?? 'Lỗi tải lên không xác định';
            }
            elseif ($_FILES['image']['size'] > $maxFileSize) {
                $errors[] = 'Kích thước hình ảnh quá lớn. Vui lòng chọn file nhỏ hơn 5MB.';
            }
            elseif (!in_array($fileType, $allowedTypes)) {
                $errors[] = 'Định dạng file không hợp lệ. Vui lòng chọn file JPG, JPEG, PNG, GIF hoặc WEBP.';
            }
            elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $errors[] = 'Không thể lưu hình ảnh. Vui lòng thử lại.';
            }
            else {
                $event->image = "uploads/events/" . $fileName;
            }
        }
    }
    
    if (empty($errors)) {
        if ($event->create()) {
            $message = '<div class="alert alert-success">🎉 Sự kiện đã được thêm thành công!</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ Lỗi khi thêm sự kiện vào database. Vui lòng thử lại!</div>';
        }
    } else {
        $message = '<div class="alert alert-danger"><ul class="mb-0">';
        foreach ($errors as $error) {
            $message .= '<li>' . $error . '</li>';
        }
        $message .= '</ul></div>';
    }
}

 $pageTitle = "Thêm Sự Kiện Mới";
require_once '../includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4 text-center text-primary fw-bold">➕ Thêm Sự Kiện Mới</h2>
    <?php echo $message; ?>

    <form method="POST" enctype="multipart/form-data" class="p-4 bg-light rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Tên sự kiện</label>
            <input type="text" name="title" class="form-control" placeholder="Nhập tên sự kiện" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả sự kiện"></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Ngày tổ chức</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Giờ bắt đầu</label>
                <input type="time" name="time" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Địa điểm</label>
            <input type="text" name="location" class="form-control" placeholder="Nhập địa điểm tổ chức" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="upcoming">Sắp diễn ra</option>
                <option value="ongoing">Đang diễn ra</option>
                <option value="ended">Đã kết thúc</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh sự kiện</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <div class="form-text">Chấp nhận các định dạng: JPG, JPEG, PNG, GIF, WEBP. Tối đa 5MB.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Lưu Sự Kiện</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>