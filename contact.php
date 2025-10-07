<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        setFlash('Vui lòng nhập đầy đủ thông tin', 'danger');
    } else {
        setFlash('Cảm ơn bạn đã liên hệ với chúng tôi. Chúng tôi sẽ phản hồi trong vòng 24 giờ.', 'success');
        redirect(SITE_URL . '/contact.php');
    }
}

 $pageTitle = "Liên Hệ";
require_once 'includes/header.php';
?>

<section class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="contact-hero">
                    <div class="text-center">
                        <h1 class="display-4 fw-bold text-white mb-4">Liên Hệ Với Chúng Tôi</h1>
                        <p class="lead text-white">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-2">Thông Tin Liên Hệ</h2>
                <p class="text-center text-muted mb-5">Hãy liên hệ với chúng tôi nếu bạn có bất kỳ câu hỏi hoặc yêu cầu nào</p>
                
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="contact-info-card">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="contact-icon">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                </div>
                                <h3 class="text-center mb-3">Địa Chỉ</h3>
                                <p class="text-center"><?php echo ADDRESS; ?></p>
                                <div class="text-center mt-3">
                                    <a href="https://maps.google.com/?q=<?php echo urlencode(ADDRESS); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Xem Bản Đồ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="contact-info-card">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="contact-icon">
                                        <i class="bi bi-telephone-fill"></i>
                                    </div>
                                </div>
                                <h3 class="text-center mb-3">Điện Thoại</h3>
                                <p class="text-center"><?php echo PHONE; ?></p>
                                <div class="text-center mt-3">
                                    <a href="tel:<?php echo str_replace([' ', '(', ')', '-'], '', PHONE); ?>" class="btn btn-sm btn-outline-primary">Gọi Ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="contact-info-card">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="contact-icon">
                                        <i class="bi bi-envelope-fill"></i>
                                    </div>
                                </div>
                                <h3 class="text-center mb-3">Email</h3>
                                <p class="text-center"><?php echo EMAIL; ?></p>
                                <div class="text-center mt-3">
                                    <a href="mailto:<?php echo EMAIL; ?>" class="btn btn-sm btn-outline-primary">Gửi Email</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Gửi Tin Nhắn Cho Chúng Tôi</h2>
                
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="contact-form-card card">
                            <div class="card-body p-4">
                                <?php 
                                $flash = getFlash();
                                if ($flash): 
                                ?>
                                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $flash['message']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php endif; ?>
                                
                                <form method="post" action="<?php echo SITE_URL; ?>/contact.php">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label fw-bold">Họ và tên</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ và tên của bạn" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label fw-bold">Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="subject" class="form-label fw-bold">Chủ đề</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-chat-dots"></i></span>
                                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Nhập chủ đề" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="message" class="form-label fw-bold">Tin nhắn</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-pencil"></i></span>
                                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Nhập nội dung tin nhắn của bạn" required></textarea>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">Gửi Tin Nhắn</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Văn Phòng Của Chúng Tôi</h2>
                
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="office-card card h-100">
                            <div class="card-body">
                                <h4 class="card-title text-center mb-4">Văn Phòng Chính</h4>
                                <div class="office-info">
                                    <p class="mb-3"><i class="bi bi-geo-alt-fill text-primary me-2"></i><strong>Địa chỉ:</strong> Đại Học Tài Chính Ngân Hàng Hà Nội, Mê Linh</p>
                                    <p class="mb-3"><i class="bi bi-telephone-fill text-primary me-2"></i><strong>Điện thoại:</strong> <?php echo PHONE; ?></p>
                                    <p class="mb-0"><i class="bi bi-clock-fill text-primary me-2"></i><strong>Giờ làm việc:</strong> Thứ 2 - Thứ 6: 8:00 - 18:00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="office-card card h-100">
                            <div class="card-body">
                                <h4 class="card-title text-center mb-4">Văn Phòng Chi Nhánh</h4>
                                <div class="office-info">
                                    <p class="mb-3"><i class="bi bi-geo-alt-fill text-primary me-2"></i><strong>Địa chỉ:</strong> Đại Học Tài Chính Ngân Hàng Hà Nội, Dịch Vọng Hậu</p>
                                    <p class="mb-3"><i class="bi bi-telephone-fill text-primary me-2"></i><strong>Điện thoại:</strong> (024) 9876 5432</p>
                                    <p class="mb-0"><i class="bi bi-clock-fill text-primary me-2"></i><strong>Giờ làm việc:</strong> Thứ 2 - Thứ 6: 8:30 - 17:30</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>