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

$currentUser = null;
if (isLoggedIn()) {
    require_once '../classes/User.php';
    $userObj = new User($db);
    $currentUser = $userObj->getUserById($_SESSION['user_id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    $registration->event_id = $event_id;
    $registration->user_id = $_SESSION['user_id'];
    $registration->registration_date = date('Y-m-d');
    $registration->status = 'pending';

    if ($registration->checkRegistration()) {
        setFlash('Bạn đã đăng ký sự kiện này rồi', 'warning');
    } else {
        if ($registration->create()) {
            setFlash('Đăng ký sự kiện thành công! Vui lòng chờ quản trị viên duyệt', 'success');
        } else {
            setFlash('Đăng ký sự kiện thất bại, vui lòng thử lại', 'danger');
        }
    }

    redirect(SITE_URL . '/events/detail.php?id=' . $event_id);
}

$registration->event_id = $event_id;
$attendees = $registration->readByEvent();

$pageTitle = $event_data['title'];
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="event-detail-header">
        <?php
        $eventImage = $event_data['image'] ?? '';
        $defaultImage = SITE_URL . '/assets/images/default-event.jpg';
        $imagePath = (!empty($eventImage))
            ? (str_starts_with($eventImage, 'uploads/') ? SITE_URL . '/' . $eventImage : SITE_URL . '/uploads/events/' . $eventImage)
            : $defaultImage;
        ?>
        <img src="<?php echo htmlspecialchars($imagePath); ?>" class="event-detail-img" alt="<?php echo htmlspecialchars($event_data['title']); ?>" onerror="this.src='<?php echo $defaultImage; ?>'">

        <div class="event-detail-overlay">
            <h1 class="event-detail-title"><?php echo htmlspecialchars($event_data['title']); ?></h1>
            <div class="event-detail-date">
                <i class="bi bi-calendar-event me-2"></i>
                <?php echo formatDate($event_data['date']); ?> | <?php echo htmlspecialchars($event_data['time']); ?>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="row">
        <!-- CỘT TRÁI -->
        <div class="col-lg-8">
            <div class="event-detail-content">
                <div class="content-section">
                    <h3 class="content-title">Thông tin sự kiện</h3>
                    <div class="event-detail-meta">
                        <div class="meta-item"><i class="bi bi-geo-alt meta-icon"></i> <span><?php echo htmlspecialchars($event_data['location']); ?></span></div>
                        <div class="meta-item"><i class="bi bi-building meta-icon"></i> <span><?php echo htmlspecialchars($event_data['organizer_name'] ?? 'Chưa có thông tin'); ?></span></div>
                        <div class="meta-item"><i class="bi bi-tag meta-icon"></i> <span><?php echo htmlspecialchars($event_data['category_name'] ?? 'Không có danh mục'); ?></span></div>
                        <div class="meta-item"><i class="bi bi-people meta-icon"></i>
                            <span><?php echo count($attendees); ?>/<?php echo htmlspecialchars($event_data['max_attendees'] ?? '∞'); ?> người tham gia</span>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h3 class="content-title">Mô tả sự kiện</h3>
                    <div class="event-description">
                        <?php echo nl2br(htmlspecialchars($event_data['description'])); ?>
                    </div>
                </div>
            </div>

            <div class="attendees-list mt-4">
                <h3 class="content-title">Danh Sách Người Tham Gia</h3>
                <div id="attendeesListContent">
                    <?php if (count($attendees) > 0): ?>
                        <div class="attendees-grid">
                            <?php foreach ($attendees as $attendee): ?>
                                <div class="attendee-item">
                                    <div class="attendee-avatar">
                                        <?php
                                        $avatarPath = !empty($attendee['avatar'])
                                            ? SITE_URL . '/uploads/avatars/' . htmlspecialchars($attendee['avatar'])
                                            : 'https://randomuser.me/api/portraits/' . (rand(0, 1) ? 'men' : 'women') . '/' . rand(1, 90) . '.jpg';
                                        ?>
                                        <img src="<?php echo $avatarPath; ?>" alt="<?php echo htmlspecialchars($attendee['user_name'] ?? 'Người dùng'); ?>">
                                    </div>
                                    <div class="attendee-info">
                                        <div class="attendee-name"><?php echo htmlspecialchars($attendee['user_name'] ?? 'Ẩn danh'); ?></div>
                                        <div class="attendee-email"><?php echo htmlspecialchars($attendee['user_email'] ?? 'Không có email'); ?></div>
                                    </div>
                                    <span class="attendee-status status-<?php echo htmlspecialchars($attendee['status']); ?>">
                                        <?php 
                                            if ($attendee['status'] == 'approved') echo 'Đã duyệt';
                                            elseif ($attendee['status'] == 'rejected') echo 'Đã từ chối';
                                            else echo 'Chờ duyệt';
                                        ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state text-center">
                            <i class="bi bi-people display-4"></i>
                            <h5 class="empty-state-title mt-3">Chưa có người tham gia</h5>
                            <p class="text-muted">Sự kiện này chưa có người đăng ký.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI (FORM ĐĂNG KÝ) -->
        <div class="col-lg-4">
            <div class="registration-card">
                <div class="card-header text-center bg-primary text-white">
                    <h3 class="content-title mb-0">Đăng Ký Tham Gia</h3>
                </div>
                <div class="card-body">
                    <?php if (!isLoggedIn()): ?>
                        <div class="text-center mb-4">
                            <i class="bi bi-person-circle display-4 text-primary"></i>
                            <p class="mt-3">Bạn cần đăng nhập để đăng ký tham gia sự kiện này.</p>
                            <a href="<?php echo SITE_URL; ?>/auth/login.php?redirect=<?php echo urlencode(SITE_URL . '/events/detail.php?id=' . $event_id); ?>" class="btn btn-primary w-100">Đăng Nhập</a>
                            <p class="mt-2 mb-0">Chưa có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/register.php">Đăng ký ngay</a></p>
                        </div>
                    <?php else: ?>
                        <?php 
                        $registration->event_id = $event_id;
                        $registration->user_id = $_SESSION['user_id'];
                        $is_registered = $registration->checkRegistration();

                        if ($is_registered):
                            $registrations = $registration->readByUser();
                            foreach ($registrations as $reg) {
                                if ($reg['event_id'] == $event_id) $user_registration = $reg;
                            }
                            if (!empty($user_registration)): ?>
                                <div class="text-center mb-4">
                                    <i class="bi bi-check-circle display-4 text-success"></i>
                                    <h5 class="mt-3">Bạn đã đăng ký tham gia</h5>
                                    <div class="mb-3">
                                        <span class="badge bg-<?php echo ($user_registration['status'] == 'approved') ? 'success' : (($user_registration['status'] == 'rejected') ? 'danger' : 'warning'); ?>">
                                            <?php 
                                                if ($user_registration['status'] == 'approved') echo 'Đã duyệt';
                                                elseif ($user_registration['status'] == 'rejected') echo 'Đã từ chối';
                                                else echo 'Chờ duyệt';
                                            ?>
                                        </span>
                                    </div>
                                    <p class="text-muted">Ngày đăng ký: <?php echo formatDate($user_registration['registration_date']); ?></p>
                                </div>
                                <div class="d-grid">
                                    <a href="<?php echo SITE_URL; ?>/user/my-events.php" class="btn btn-outline-primary">Xem Sự Kiện Của Tôi</a>
                                </div>
                            <?php endif; else: ?>
                                <?php 
                                $max_attendees = $event_data['max_attendees'] ?? 0;
                                if ($max_attendees > 0 && count($attendees) >= $max_attendees): ?>
                                    <div class="text-center mb-4">
                                        <i class="bi bi-x-circle display-4 text-danger"></i>
                                        <h5 class="mt-3">Sự kiện đã đủ người tham gia</h5>
                                        <p class="text-muted">Rất tiếc, sự kiện này đã đủ số lượng người tham gia.</p>
                                    </div>
                                    <div class="d-grid"><button class="btn btn-secondary" disabled>Hết chỗ</button></div>
                                <?php else: ?>

                                    <!-- FORM ĐẦY ĐỦ -->
                                    <form method="post" action="<?php echo SITE_URL; ?>/events/detail.php?id=<?php echo $event_id; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Họ và tên</label>
                                            <input type="text" class="form-control" name="fullname" 
                                                value="<?php echo htmlspecialchars($currentUser['full_name'] ?? $currentUser['username'] ?? ''); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" 
                                                value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Số điện thoại</label>
                                            <input type="tel" class="form-control" name="phone" placeholder="Nhập số điện thoại của bạn"
                                                value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="organization" class="form-label">Tổ chức / Công ty</label>
                                            <input type="text" class="form-control" id="organization" name="organization"
                                                placeholder="Nhập tên tổ chức hoặc công ty (nếu có)">
                                        </div>

                                        <div class="mb-3">
                                            <label for="note" class="form-label">Ghi chú</label>
                                            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Ghi chú thêm (nếu có)"></textarea>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                            <label class="form-check-label" for="agreeTerms">
                                                Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">điều khoản và điều kiện</a>
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="receiveUpdates" checked>
                                            <label class="form-check-label" for="receiveUpdates">Tôi muốn nhận thông tin cập nhật về sự kiện</label>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-check-circle me-1"></i> Đăng Ký Tham Gia
                                        </button>
                                    </form>

                                <?php endif; endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ĐIỀU KHOẢN -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều Khoản và Điều Kiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Điều khoản đăng ký</h6>
                <p>Bằng cách đăng ký, bạn đồng ý cung cấp thông tin chính xác và đầy đủ.</p>
                <h6>2. Chính sách hủy đăng ký</h6>
                <p>Bạn có thể hủy trước 24h so với thời gian bắt đầu sự kiện.</p>
                <h6>3. Thay đổi sự kiện</h6>
                <p>Chúng tôi có thể thay đổi thời gian, địa điểm nếu cần thiết.</p>
                <h6>4. Trách nhiệm</h6>
                <p>Bạn chịu trách nhiệm về hành vi và tài sản cá nhân trong suốt sự kiện.</p>
                <h6>5. Sử dụng hình ảnh</h6>
                <p>Bằng cách tham gia, bạn đồng ý cho phép sử dụng hình ảnh phục vụ quảng bá.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(eventId) {
    if (confirm('Bạn có chắc chắn muốn xóa sự kiện này? Hành động này không thể hoàn tác.')) {
        window.location.href = '<?php echo SITE_URL; ?>/admin/events.php?action=delete&id=' + eventId;
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
