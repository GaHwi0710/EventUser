<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php');
}

require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Event.php';
require_once '../classes/Registration.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$user_data = $user->getUserById($_SESSION['user_id']);

$registration = new Registration($db);
$registration->user_email = $_SESSION['user_email']; 
$user_registrations = $registration->readByUserDashboard(); 

$event = new Event($db);
$upcoming_events = $event->readUpcoming();

$pageTitle = "Bảng Điều Khiển";
require_once '../includes/header.php';
?>

<section class="dashboard-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="dashboard-sidebar">
                    <div class="user-profile text-center">
                        <?php
                        $avatarPath = !empty($user_data['avatar'])
                            ? SITE_URL . '/uploads/avatars/' . htmlspecialchars($user_data['avatar'])
                            : 'https://cdn-icons-png.flaticon.com/512/847/847969.png';
                        ?>
                        <div class="profile-avatar mb-3">
                            <img src="<?php echo $avatarPath; ?>" class="rounded-circle" alt="User Avatar" width="120" height="120">
                        </div>

                        <h5 class="mb-1"><?php echo htmlspecialchars($user_data['full_name']); ?></h5>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($user_data['email']); ?></p>

                        <span class="badge bg-<?php echo ($user_data['role'] === 'admin') ? 'danger' : 'primary'; ?>">
                            <?php echo ($user_data['role'] === 'admin') ? 'Quản trị viên' : 'Người dùng'; ?>
                        </span>
                    </div>

                    <div class="sidebar-menu mt-4">
                        <div class="menu-item active">
                            <a href="<?php echo SITE_URL; ?>/user/dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> Bảng điều khiển
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="<?php echo SITE_URL; ?>/user/profile.php">
                                <i class="bi bi-person me-2"></i> Hồ sơ cá nhân
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="<?php echo SITE_URL; ?>/user/my-events.php">
                                <i class="bi bi-calendar-event me-2"></i> Sự kiện của tôi
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="<?php echo SITE_URL; ?>/user/settings.php">
                                <i class="bi bi-gear me-2"></i> Cài đặt
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="<?php echo SITE_URL; ?>/auth/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="dashboard-welcome mb-4">
                    <h2>Chào mừng, <?php echo htmlspecialchars($user_data['full_name']); ?>!</h2>
                    <p>Đây là bảng điều khiển cá nhân của bạn. Tại đây, bạn có thể xem thông tin cá nhân, các sự kiện đã đăng ký và quản lý tài khoản.</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-primary"><i class="bi bi-calendar-check"></i></div>
                            <div class="stats-info">
                                <h3><?php echo count($user_registrations); ?></h3>
                                <p>Sự kiện đã đăng ký</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-success"><i class="bi bi-check-circle"></i></div>
                            <div class="stats-info">
                                <h3>
                                    <?php 
                                    $approved_count = array_reduce($user_registrations, fn($c, $r) => $c + ($r['status'] === 'approved' ? 1 : 0), 0);
                                    echo $approved_count;
                                    ?>
                                </h3>
                                <p>Sự kiện đã duyệt</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-warning"><i class="bi bi-clock"></i></div>
                            <div class="stats-info">
                                <h3>
                                    <?php 
                                    $pending_count = array_reduce($user_registrations, fn($c, $r) => $c + ($r['status'] === 'pending' ? 1 : 0), 0);
                                    echo $pending_count;
                                    ?>
                                </h3>
                                <p>Sự kiện chờ duyệt</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Sự kiện sắp diễn ra</h3>
                        <a href="<?php echo SITE_URL; ?>/events/index.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($upcoming_events)): ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Sự kiện</th>
                                            <th>Ngày</th>
                                            <th>Địa điểm</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcoming_events as $event): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                                <td><?php echo formatDate($event['date']); ?></td>
                                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                                <td><a href="<?php echo SITE_URL; ?>/events/detail.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-primary">Chi tiết</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state text-center">
                                <i class="bi bi-calendar-x display-5"></i>
                                <h5>Không có sự kiện sắp diễn ra</h5>
                                <p>Hiện tại không có sự kiện nào sắp diễn ra.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
