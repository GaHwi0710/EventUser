<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/Event.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    redirect(SITE_URL . '/index.php');
}

$database = new Database();
$db = $database->getConnection();
$event = new Event($db);

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($event_id <= 0) redirect('events.php');

$event->id = $event_id;
$eventData = $event->readOne();

if (!$eventData) {
    setFlash("Không tìm thấy sự kiện!", "danger");
    redirect('events.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event->id = $event_id;
    $event->title = $_POST['title'] ?? '';
    $event->slug = $_POST['slug'] ?? '';
    $event->description = $_POST['description'] ?? '';
    $event->short_description = $_POST['short_description'] ?? '';
    $event->date = $_POST['date'] ?? '';
    $event->time = $_POST['time'] ?? '';
    $event->start_date = $_POST['date'] ?? '';
    $event->end_date = $_POST['end_date'] ?? '';
    $event->location = $_POST['location'] ?? '';
    $event->category_name = $_POST['category_name'] ?? '';
    $event->organizer_name = $_POST['organizer_name'] ?? '';
    $event->max_attendees = $_POST['max_attendees'] ?? 0;
    $event->price = $_POST['price'] ?? 0;
    $event->status = $_POST['status'] ?? 'draft';
    $event->featured = isset($_POST['featured']) ? 1 : 0;

    if (empty($event->slug)) {
        $event->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $event->title), '-'));
    }

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/events/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $event->image = $fileName;
            } else {
                $event->image = $eventData['image'];
            }
        } else {
            setFlash("Định dạng ảnh không hợp lệ!", "danger");
            $event->image = $eventData['image'];
        }
    } else {
        $event->image = $eventData['image'];
    }

    if ($event->update()) {
        setFlash("✅ Cập nhật sự kiện thành công!", "success");
        redirect("events.php");
    } else {
        setFlash("⚠️ Có lỗi xảy ra khi cập nhật!", "danger");
    }
}

$pageTitle = "Chỉnh sửa sự kiện";
require_once '../includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4 text-primary fw-bold"><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa sự kiện</h2>

    <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white shadow-sm rounded p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($eventData['title']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Đường dẫn (Slug)</label>
                <input type="text" name="slug" class="form-control" placeholder="vd: hoi-thao-cong-nghe-2025"
                       value="<?php echo htmlspecialchars($eventData['slug']); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Người tổ chức</label>
                <input type="text" name="organizer_name" class="form-control" value="<?php echo htmlspecialchars($eventData['organizer_name']); ?>">
            </div>

            <div class="col-12">
                <label class="form-label">Mô tả ngắn</label>
                <input type="text" name="short_description" class="form-control" value="<?php echo htmlspecialchars($eventData['short_description']); ?>">
            </div>

            <div class="col-12">
                <label class="form-label">Mô tả chi tiết</label>
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($eventData['description']); ?></textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label">Ngày bắt đầu</label>
                <input type="datetime-local" name="date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($eventData['date'])); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Ngày kết thúc</label>
                <input type="datetime-local" name="end_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($eventData['end_date'])); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Địa điểm</label>
                <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($eventData['location']); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Danh mục</label>
                <input type="text" name="category_name" class="form-control" value="<?php echo htmlspecialchars($eventData['category_name']); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Số người tối đa</label>
                <input type="number" name="max_attendees" class="form-control" value="<?php echo htmlspecialchars($eventData['max_attendees']); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Giá vé (VND)</label>
                <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($eventData['price']); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <?php
                    $statusList = ['draft'=>'Bản nháp', 'published'=>'Đã xuất bản', 'cancelled'=>'Hủy bỏ'];
                    foreach ($statusList as $key => $label) {
                        $selected = ($eventData['status'] === $key) ? 'selected' : '';
                        echo "<option value='$key' $selected>$label</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-check-label">Nổi bật</label>
                <input type="checkbox" name="featured" value="1" <?php echo ($eventData['featured'] == 1) ? 'checked' : ''; ?>>
            </div>

            <div class="col-md-6">
                <label class="form-label">Ảnh sự kiện</label>
                <input type="file" name="image" class="form-control">
                <?php if (!empty($eventData['image'])): ?>
                    <div class="mt-2">
                        <img src="../uploads/events/<?php echo htmlspecialchars($eventData['image']); ?>" alt="Event Image" class="img-fluid rounded shadow-sm" width="200">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <a href="events.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu thay đổi</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
