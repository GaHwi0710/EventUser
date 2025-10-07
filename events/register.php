<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    setFlash('Bạn cần đăng nhập để đăng ký tham gia sự kiện', 'warning');
    redirect(SITE_URL . '/auth/login.php?redirect=' . urlencode(SITE_URL . '/events/register.php?id=' . $_GET['id']));
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlash('Sự kiện không tồn tại', 'danger');
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
 $registration->event_id = $event_id;
 $registration->user_id = $_SESSION['user_id'];

if ($registration->checkRegistration()) {
    setFlash('Bạn đã đăng ký sự kiện này rồi', 'warning');
    redirect(SITE_URL . '/events/detail.php?id=' . $event_id);
}

 $attendees = $registration->readByEvent();
 $max_attendees = isset($event_data['max_attendees']) ? $event_data['max_attendees'] : 0;

if ($max_attendees > 0 && count($attendees) >= $max_attendees) {
    setFlash('Sự kiện đã đủ số lượng người tham gia', 'warning');
    redirect(SITE_URL . '/events/detail.php?id=' . $event_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration->event_id = $event_id;
    $registration->user_id = $_SESSION['user_id'];
    $registration->registration_date = date('Y-m-d');
    $registration->status = 'pending';
    
    if ($registration->create()) {
        setFlash('Đăng ký sự kiện thành công! Vui lòng chờ quản trị viên duyệt', 'success');
        redirect(SITE_URL . '/user/my-events.php');
    } else {
        setFlash('Đăng ký sự kiện thất bại, vui lòng thử lại', 'danger');
    }
}

 $pageTitle = "Đăng Ký Sự Kiện";
require_once '../includes/header.php';
?>

<section class="event-registration-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="registration-card">
                    <div class="card-header">
                        <h2>Đăng Ký Tham Gia Sự Kiện</h2>
                    </div>
                    <div class="card-body">
                        <?php 
                        $flash = getFlash();
                        if ($flash): 
                        ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>
                        
                        <div class="event-info">
                            <div class="event-image">
                                <img src="<?php echo $event_data['image']; ?>" class="img-fluid" alt="<?php echo $event_data['title']; ?>">
                            </div>
                            <div class="event-details">
                                <h3><?php echo $event_data['title']; ?></h3>
                                <div class="event-meta">
                                    <p><i class="bi bi-calendar-event me-2"></i> <?php echo formatDate($event_data['date']); ?> | <?php echo $event_data['time']; ?></p>
                                    <p><i class="bi bi-geo-alt me-2"></i> <?php echo $event_data['location']; ?></p>
                                    <p><i class="bi bi-building me-2"></i> <?php echo $event_data['organizer']; ?></p>
                                    <p><i class="bi bi-people me-2"></i> <?php echo count($attendees); ?>/<?php echo $max_attendees; ?> người tham gia</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="event-description">
                            <h4>Mô tả sự kiện</h4>
                            <p><?php echo nl2br($event_data['description']); ?></p>
                        </div>
                        
                        <div class="registration-form">
                            <h4>Thông tin đăng ký</h4>
                            <form method="post" action="<?php echo SITE_URL; ?>/events/register.php?id=<?php echo $event_id; ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label">Họ và tên</label>
                                        <input type="text" class="form-control" id="full_name" value="<?php echo $_SESSION['user_name']; ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="<?php echo $event_data['email']; ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại của bạn" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="organization" class="form-label">Tổ chức/Công ty</label>
                                        <input type="text" class="form-control" id="organization" name="organization" placeholder="Nhập tên tổ chức/công ty">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="note" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="note" name="note" rows="3" placeholder="Nhập ghi chú (nếu có)"></textarea>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                    <label class="form-check-label" for="agreeTerms">
                                        Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">điều khoản và điều kiện</a> của sự kiện
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="receiveUpdates" checked>
                                    <label class="form-check-label" for="receiveUpdates">
                                        Tôi muốn nhận cập nhật về sự kiện qua email
                                    </label>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo SITE_URL; ?>/events/detail.php?id=<?php echo $event_id; ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i> Đăng Ký Ngay
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều Khoản và Điều Kiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Điều khoản đăng ký</h6>
                <p>Bằng cách đăng ký tham gia sự kiện, bạn đồng ý cung cấp thông tin cá nhân chính xác và đầy đủ. Bạn chịu trách nhiệm về tính chính xác của thông tin đã cung cấp.</p>
                
                <h6>2. Chính sách hủy đăng ký</h6>
                <p>Bạn có thể hủy đăng ký tham gia sự kiện trước 24 giờ so với thời gian bắt đầu sự kiện. Sau thời điểm này, chúng tôi sẽ không hoàn lại phí đăng ký (nếu có).</p>
                
                <h6>3. Thay đổi sự kiện</h6>
                <p>Chúng tôi có quyền thay đổi thời gian, địa điểm hoặc nội dung sự kiện nếu cần thiết. Trong trường hợp này, chúng tôi sẽ thông báo cho bạn sớm nhất có thể.</p>
                
                <h6>4. Trách nhiệm cá nhân</h6>
                <p>Bạn chịu trách nhiệm về hành vi và tài sản cá nhân trong suốt thời gian tham gia sự kiện. Chúng tôi không chịu trách nhiệm cho bất kỳ tổn thất hoặc thiệt hại nào xảy ra do lỗi của bạn.</p>
                
                <h6>5. Sử dụng hình ảnh</h6>
                <p>Bằng cách tham gia sự kiện, bạn đồng ý cho chúng tôi sử dụng hình ảnh của bạn cho mục đích quảng cáo và truyền thông của sự kiện.</p>
                
                <h6>6. Liên hệ</h6>
                <p>Mọi thắc mắc hoặc khiếu nại xin vui lòng liên hệ với chúng tôi qua email <?php echo EMAIL; ?> hoặc số điện thoại <?php echo PHONE; ?>.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>