<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect(SITE_URL . '/index.php');
}

require_once '../classes/Database.php';
require_once '../classes/Event.php';
require_once '../classes/User.php';
require_once '../classes/Registration.php';

$database = new Database();
$db = $database->getConnection();

$event = new Event($db);
$user = new User($db);
$registration = new Registration($db);

$events = $event->readAll();
$users = $user->readAll();
$registrations = $registration->readAll();
$upcoming_events = $event->readUpcoming();

$pageTitle = "Bảng Điều Khiển Quản Trị";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Bảng Điều Khiển Quản Trị</h1>
        <p class="dashboard-subtitle">
            Chào mừng bạn trở lại, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>!
        </p>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card primary">
                <div class="stats-icon"><i class="bi bi-calendar-event"></i></div>
                <div class="stats-number"><?php echo count($events); ?></div>
                <div class="stats-label">Tổng Sự Kiện</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card success">
                <div class="stats-icon"><i class="bi bi-people"></i></div>
                <div class="stats-number"><?php echo count($users); ?></div>
                <div class="stats-label">Tổng Người Dùng</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card warning">
                <div class="stats-icon"><i class="bi bi-clipboard-check"></i></div>
                <div class="stats-number"><?php echo count($registrations); ?></div>
                <div class="stats-label">Tổng Đăng Ký</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card danger">
                <div class="stats-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="stats-number"><?php echo count($upcoming_events); ?></div>
                <div class="stats-label">Sự Kiện Sắp Diễn Ra</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Sự Kiện Mới Nhất</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Tiêu đề</th><th>Ngày</th><th>Trạng thái</th></tr></thead>
                            <tbody>
                                <?php 
                                $recent_events = array_slice($events, 0, 5);
                                foreach ($recent_events as $event_item): ?>
                                <tr>
                                    <td><a href="<?php echo SITE_URL; ?>/events/detail.php?id=<?php echo $event_item['id']; ?>">
                                        <?php echo htmlspecialchars($event_item['title']); ?></a>
                                    </td>
                                    <td><?php echo formatDate($event_item['date']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo ($event_item['status'] == 'published') ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($event_item['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Đăng Ký Mới Nhất</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Sự kiện</th><th>Người dùng</th><th>Trạng thái</th></tr></thead>
                            <tbody>
                                <?php 
                                $recent_registrations = array_slice($registrations, 0, 5);
                                foreach ($recent_registrations as $reg): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['user_email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $reg['status'] == 'approved' ? 'success' : 
                                                 ($reg['status'] == 'rejected' ? 'danger' : 'warning'); ?>">
                                            <?php 
                                            if ($reg['status'] == 'approved') echo 'Đã duyệt';
                                            elseif ($reg['status'] == 'rejected') echo 'Đã từ chối';
                                            else echo 'Chờ duyệt';
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
