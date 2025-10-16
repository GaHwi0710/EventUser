<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php');
}

require_once '../classes/Database.php';
require_once '../classes/Registration.php';
require_once '../classes/Event.php';

$database = new Database();
$db = $database->getConnection();

$registration = new Registration($db);
$registration->user_email = $_SESSION['user_email']; 
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
                $imagePath = !empty($reg['event_image'])
                    ? SITE_URL . '/uploads/events/' . htmlspecialchars($reg['event_image'])
                    : SITE_URL . '/assets/images/default-event.jpg';
                ?>
                <div class="col-md-4 mb-4">
                    <a href="<?php echo SITE_URL; ?>/events/detail.php?id=<?php echo urlencode($reg['event_id']); ?>" 
                       class="text-decoration-none text-dark">
                        <div class="card shadow-sm border-0 h-100">
                            <img src="<?php echo $imagePath; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($reg['event_title']); ?>" 
                                 height="200" 
                                 style="object-fit: cover; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
                            <div class="card-body">
                                <h5 class="fw-semibold mb-2"><?php echo htmlspecialchars($reg['event_title']); ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar-event"></i> 
                                    <?php echo formatDate($reg['registration_date']); ?>
                                </p>
                                <span class="badge bg-<?php 
                                    echo $reg['status']=='approved' ? 'success' : 
                                        ($reg['status']=='rejected' ? 'danger' : 'warning'); ?>">
                                    <?php 
                                    if ($reg['status']=='approved') echo 'Đã duyệt';
                                    elseif ($reg['status']=='rejected') echo 'Đã từ chối';
                                    else echo 'Chờ duyệt';
                                    ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted mt-5">Bạn chưa đăng ký sự kiện nào.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.dashboard-title {
    font-size: 2rem;
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}
.card:hover {
    transform: translateY(-4px);
    transition: 0.3s;
}
.card img {
    border-radius: .5rem .5rem 0 0;
}
</style>

<?php require_once '../includes/footer.php'; ?>
