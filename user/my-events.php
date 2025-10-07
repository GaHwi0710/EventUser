<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php');
}

require_once '../classes/Database.php';
require_once '../classes/Registration.php';

$database = new Database();
$db = $database->getConnection();

$registration = new Registration($db);
$registration->user_id = $_SESSION['user_id'];

$registrations = $registration->readByUser();

$pageTitle = "Sự Kiện Của Tôi";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="dashboard-title">Sự Kiện Của Tôi</h1>

    <div class="row">
        <?php if (count($registrations) > 0): ?>
            <?php foreach ($registrations as $reg): ?>
                <?php
                $imagePath = !empty($reg['image'])
                    ? SITE_URL . '/uploads/events/' . htmlspecialchars($reg['image'])
                    : SITE_URL . '/assets/images/default-event.jpg';
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo $imagePath; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($reg['title']); ?>" height="200" style="object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($reg['title']); ?></h5>
                            <p class="card-text mb-1">
                                <i class="bi bi-calendar-event me-1"></i> <?php echo formatDate($reg['date']); ?> | <?php echo $reg['time']; ?>
                            </p>
                            <p class="card-text mb-3">
                                <i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($reg['location']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?php echo $reg['status'] == 'approved' ? 'success' : ($reg['status'] == 'rejected' ? 'danger' : 'warning'); ?>">
                                    <?php 
                                    if ($reg['status'] == 'approved') echo 'Đã duyệt';
                                    elseif ($reg['status'] == 'rejected') echo 'Đã từ chối';
                                    else echo 'Chờ duyệt';
                                    ?>
                                </span>
                                <a href="<?php echo SITE_URL; ?>/events/detail.php?id=<?php echo $reg['event_id']; ?>" class="btn btn-sm btn-primary">Xem Chi Tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state text-center py-5">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-calendar-x display-5 text-secondary"></i>
                    </div>
                    <h4 class="empty-state-title mb-2">Bạn chưa đăng ký sự kiện nào</h4>
                    <p class="empty-state-text mb-3 text-muted">Hãy khám phá và đăng ký tham gia các sự kiện hấp dẫn.</p>
                    <a href="<?php echo SITE_URL; ?>/events/index.php" class="btn btn-primary">Khám Phá Sự Kiện</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
