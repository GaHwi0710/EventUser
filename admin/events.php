<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect(SITE_URL . '/index.php');
}

require_once '../classes/Database.php';
require_once '../classes/Event.php';
require_once '../classes/Registration.php';

$database = new Database();
$db = $database->getConnection();

$event = new Event($db);
$registration = new Registration($db);

$action = $_GET['action'] ?? '';

if ($action === 'delete' && isset($_GET['id'])) {
    $event->id = $_GET['id'];
    if ($event->delete()) {
        setFlash('Xóa sự kiện thành công', 'success');
    } else {
        setFlash('Xóa sự kiện thất bại', 'danger');
    }
    redirect(SITE_URL . '/admin/events.php');
}

$events = $event->readAll();

$pageTitle = "Quản Lý Sự Kiện";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="dashboard-title">Quản Lý Sự Kiện</h1>
        <a href="<?php echo SITE_URL; ?>/admin/add-event.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Thêm Sự Kiện Mới
        </a>
    </div>

    <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Ngày diễn ra</th>
                            <th>Danh mục</th>
                            <th>Người tham gia</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($events)): ?>
                            <?php foreach ($events as $event_item): ?>
                                <?php
                                $status = $event_item['status'];
                                $badgeColor = match ($status) {
                                    'Chờ duyệt' => 'info',
                                    'Đang diễn ra' => 'success',
                                    'Kết thúc' => 'secondary',
                                    default => 'secondary'
                                };
                                ?>
                                <tr>
                                    <td><?php echo $event_item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($event_item['title']); ?></td>
                                    <td><?php echo formatDate($event_item['date']); ?></td>
                                    <td><?php echo htmlspecialchars($event_item['category_name'] ?? 'Không có danh mục'); ?></td>
                                    <td>
                                        <?php
                                        $registration->event_id = $event_item['id'];
                                        $attendees = $registration->readByEvent();
                                        echo count($attendees) . '/' . ($event_item['max_attendees'] ?? 0);
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $badgeColor; ?>">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/edit-event.php?id=<?php echo $event_item['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/events.php?action=delete&id=<?php echo $event_item['id']; ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa sự kiện này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-calendar-x"></i>
                                        </div>
                                        <h5 class="empty-state-title">Không có sự kiện nào</h5>
                                        <p class="empty-state-text text-muted">Chưa có sự kiện nào trong hệ thống.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
