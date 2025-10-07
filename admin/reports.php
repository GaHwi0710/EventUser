<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    redirect(SITE_URL . '/index.php');
}

$database = new Database();
$db = $database->getConnection();

$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalEvents = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalRegistrations = $db->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$upcomingEvents = $db->query("SELECT COUNT(*) FROM events WHERE date >= CURDATE()")->fetchColumn();
$approvedRegistrations = $db->query("SELECT COUNT(*) FROM registrations WHERE status = 'approved'")->fetchColumn();
$pendingRegistrations = $db->query("SELECT COUNT(*) FROM registrations WHERE status = 'pending'")->fetchColumn();
$rejectedRegistrations = $db->query("SELECT COUNT(*) FROM registrations WHERE status = 'rejected'")->fetchColumn();

$stmt = $db->query("SELECT id, title, date, location, status FROM events WHERE date >= CURDATE() ORDER BY date ASC LIMIT 10");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Báo Cáo & Thống Kê";
require_once '../includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4 text-primary fw-bold"><i class="bi bi-bar-chart-line me-2"></i>Báo Cáo & Thống Kê</h2>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 text-center">
                <i class="bi bi-people display-6 text-primary"></i>
                <h4 class="mt-2 fw-bold"><?php echo $totalUsers; ?></h4>
                <p class="text-muted mb-0">Người Dùng</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 text-center">
                <i class="bi bi-calendar-event display-6 text-success"></i>
                <h4 class="mt-2 fw-bold"><?php echo $totalEvents; ?></h4>
                <p class="text-muted mb-0">Tổng Sự Kiện</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 text-center">
                <i class="bi bi-check-circle display-6 text-info"></i>
                <h4 class="mt-2 fw-bold"><?php echo $approvedRegistrations; ?></h4>
                <p class="text-muted mb-0">Đăng ký Đã Duyệt</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3 text-center">
                <i class="bi bi-hourglass-split display-6 text-warning"></i>
                <h4 class="mt-2 fw-bold"><?php echo $pendingRegistrations; ?></h4>
                <p class="text-muted mb-0">Đăng ký Chờ Duyệt</p>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center">
                <i class="bi bi-x-circle display-6 text-danger"></i>
                <h4 class="mt-2 fw-bold"><?php echo $rejectedRegistrations; ?></h4>
                <p class="text-muted mb-0">Đăng ký Bị Từ Chối</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center">
                <i class="bi bi-person-check display-6 text-primary"></i>
                <h4 class="mt-2 fw-bold"><?php echo $totalRegistrations; ?></h4>
                <p class="text-muted mb-0">Tổng Đăng Ký</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 text-center">
                <i class="bi bi-calendar2-week display-6 text-success"></i>
                <h4 class="mt-2 fw-bold"><?php echo $upcomingEvents; ?></h4>
                <p class="text-muted mb-0">Sự Kiện Sắp Diễn Ra</p>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-calendar2-event me-2"></i>Sự kiện sắp diễn ra</h5>
            <a href="events.php" class="btn btn-light btn-sm">Xem tất cả</a>
        </div>
        <div class="card-body">
            <?php if (count($events) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên Sự Kiện</th>
                                <th>Ngày</th>
                                <th>Địa Điểm</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $i => $e): ?>
                                <tr>
                                    <td><?php echo $i + 1; ?></td>
                                    <td><a href="edit-event.php?id=<?php echo $e['id']; ?>" class="text-decoration-none text-primary fw-bold"><?php echo htmlspecialchars($e['title']); ?></a></td>
                                    <td><?php echo date('d/m/Y', strtotime($e['date'])); ?></td>
                                    <td><?php echo htmlspecialchars($e['location']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo ($e['status'] === 'active') ? 'success' : 
                                                 (($e['status'] === 'pending') ? 'warning' : 'secondary');
                                        ?>">
                                            <?php 
                                            echo ($e['status'] === 'active') ? 'Đang Diễn Ra' :
                                                 (($e['status'] === 'pending') ? 'Chờ Duyệt' : 'Kết Thúc');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x display-6 text-muted"></i>
                    <p class="mt-2 text-muted">Không có sự kiện nào sắp diễn ra.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
