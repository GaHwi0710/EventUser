<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlash('Vui lòng chọn sự kiện bạn muốn xem', 'warning');
    redirect(SITE_URL . '/events/index.php');
}

$event_id = $_GET['id'];

require_once '../classes/Database.php';
require_once '../classes/Event.php';
require_once '../classes/Registration.php';

$database = new Database();
$db = $database->getConnection();

$event = new Event($db);
$event->id = $event_id;
$event_data = $event->readOne();

if (!$event_data) {
    setFlash('Sự kiện không tồn tại', 'danger');
    redirect(SITE_URL . '/events/index.php');
}

$registration = new Registration($db);
$registration->event_title = $event_data['title'];

$currentUser = null;
if (isLoggedIn()) {
    require_once '../classes/User.php';
    $userObj = new User($db);
    $currentUser = $userObj->getUserById($_SESSION['user_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $registration->user_email = $currentUser['email'];
    $registration->registration_date = date('Y-m-d');
    $registration->status = 'pending';
    $registration->ticket_number = 'EVT-' . strtoupper(uniqid());
    $registration->notes = $_POST['note'] ?? '';

    if ($registration->checkRegistration()) {
        setFlash('Bạn đã đăng ký sự kiện này rồi.', 'warning');
    } else {
        if ($registration->create()) {
            setFlash('Đăng ký sự kiện thành công! Vui lòng chờ quản trị viên duyệt.', 'success');
        } else {
            setFlash('Đăng ký sự kiện thất bại, vui lòng thử lại.', 'danger');
        }
    }

    redirect(SITE_URL . '/events/detail.php?id=' . $event_id);
}

$attendees = $registration->readByEvent();

$pageTitle = $event_data['title'];
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="event-detail-header position-relative">
        <?php
        $eventImage = $event_data['image'] ?? '';
        $defaultImage = SITE_URL . '/assets/images/default-event.jpg';
        $imagePath = (!empty($eventImage))
            ? SITE_URL . '/uploads/events/' . $eventImage
            : $defaultImage;
        ?>
        <img src="<?php echo htmlspecialchars($imagePath); ?>" 
             class="img-fluid rounded-3 w-100 shadow-sm" 
             alt="<?php echo htmlspecialchars($event_data['title']); ?>" 
             style="max-height: 420px; object-fit: cover;">

        <div class="event-detail-overlay position-absolute bottom-0 start-0 w-100 p-4 bg-gradient">
            <h1 class="text-white fw-bold"><?php echo htmlspecialchars($event_data['title']); ?></h1>
            <div class="text-white-50">
                <i class="bi bi-calendar-event me-2"></i>
                <?php echo formatDate($event_data['date']); ?> | <?php echo htmlspecialchars($event_data['time']); ?>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row g-4">
        <!-- CỘT TRÁI -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin sự kiện</h3>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-2"><i class="bi bi-geo-alt text-danger me-2"></i> 
                            Địa điểm: <?php echo htmlspecialchars($event_data['location']); ?></li>
                        <li class="mb-2"><i class="bi bi-building text-success me-2"></i> 
                            Nhà tổ chức: <?php echo htmlspecialchars($event_data['organizer_name'] ?? 'Chưa có thông tin'); ?></li>
                        <li class="mb-2"><i class="bi bi-tag text-warning me-2"></i> 
                            Danh mục: <?php echo htmlspecialchars($event_data['category_name'] ?? 'Không có danh mục'); ?></li>
                    </ul>
                    <hr>
                    <h5 class="fw-semibold mb-2">Mô tả sự kiện:</h5>
                    <p><?php echo nl2br(htmlspecialchars($event_data['description'])); ?></p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3"><i class="bi bi-people text-primary me-2"></i>Danh sách người tham gia</h4>

                    <?php if (count($attendees) > 0): ?>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <?php foreach ($attendees as $a): ?>
                                <div class="col">
                                    <div class="p-3 border rounded d-flex align-items-center bg-light">
                                        <div class="me-3">
                                            <i class="bi bi-person-circle fs-2 text-secondary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($a['user_email']); ?></div>
                                            <small class="text-muted">
                                                <?php 
                                                    if ($a['status'] === 'approved') echo 'Đã duyệt';
                                                    elseif ($a['status'] === 'rejected') echo 'Đã từ chối';
                                                    else echo 'Chờ duyệt';
                                                ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-person-x fs-1 mb-2"></i>
                            <p>Chưa có người đăng ký tham gia sự kiện này.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 90px;">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Đăng ký tham gia</h4>
                </div>
                <div class="card-body">
                    <?php if (!isLoggedIn()): ?>
                        <div class="text-center">
                            <i class="bi bi-person-circle display-5 text-primary"></i>
                            <p class="mt-3">Bạn cần đăng nhập để đăng ký tham gia sự kiện này.</p>
                            <a href="<?php echo SITE_URL; ?>/auth/login.php?redirect=<?php echo urlencode(SITE_URL . '/events/detail.php?id=' . $event_id); ?>" class="btn btn-primary w-100 mb-2">Đăng nhập ngay</a>
                            <p class="text-muted small">Chưa có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/register.php">Đăng ký</a></p>
                        </div>
                    <?php else: ?>
                        <?php 
                        $registration->user_email = $currentUser['email'];
                        $is_registered = $registration->checkRegistration();
                        ?>
                        <?php if ($is_registered): ?>
                            <div class="text-center">
                                <i class="bi bi-check-circle display-5 text-success"></i>
                                <h5 class="mt-3">Bạn đã đăng ký</h5>
                                <p class="text-muted">Vui lòng chờ quản trị viên duyệt đơn đăng ký.</p>
                                <a href="<?php echo SITE_URL; ?>/user/my-events.php" class="btn btn-outline-primary w-100">Xem sự kiện của tôi</a>
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($currentUser['email']); ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ghi chú thêm</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Ghi chú cho ban tổ chức..."></textarea>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="agree" required>
                                    <label for="agree" class="form-check-label">
                                        Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">điều khoản và điều kiện</a>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-1"></i> Xác nhận đăng ký
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsLabel">Điều khoản & Điều kiện tham gia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6>1. Cam kết thông tin</h6>
        <p>Bạn đồng ý cung cấp thông tin chính xác khi đăng ký.</p>
        <h6>2. Chính sách hủy</h6>
        <p>Bạn có thể hủy tham gia trước 24h trước khi sự kiện bắt đầu.</p>
        <h6>3. Thay đổi lịch</h6>
        <p>Ban tổ chức có quyền thay đổi thời gian và địa điểm nếu cần thiết.</p>
        <h6>4. Trách nhiệm cá nhân</h6>
        <p>Bạn chịu trách nhiệm về tài sản và hành vi của mình khi tham dự.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
