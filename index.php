<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'classes/Database.php';
require_once 'classes/Event.php';

$database = new Database();
$db = $database->getConnection();

$event = new Event($db);
$upcoming_events = $event->readUpcoming();

$pageTitle = "Trang Chủ";
require_once 'includes/header.php';
?>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title animate-fade-in">Nền tảng sự kiện toàn diện</h1>
                <p class="hero-subtitle animate-fade-in">Tạo, quản lý và quảng bá sự kiện của bạn một cách chuyên nghiệp với EventUser</p>
                <div class="hero-buttons animate-fade-in">
                    <a href="<?php echo SITE_URL; ?>/events/index.php" class="btn btn-primary-custom btn-lg me-3">Khám phá sự kiện</a>
                    <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-outline-light btn-lg">Đăng ký ngay</a>
                </div>
                <div class="hero-stats animate-fade-in">
                    <div class="stat-item">
                        <h3>1000+</h3>
                        <p>Sự kiện đã tổ chức</p>
                    </div>
                    <div class="stat-item">
                        <h3>50000+</h3>
                        <p>Người tham gia</p>
                    </div>
                    <div class="stat-item">
                        <h3>98%</h3>
                        <p>Khách hàng hài lòng</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image animate-slide-in-right">
                    <img src="<?php echo SITE_URL; ?>/assets/images/banner.jpg" alt="Event Management Dashboard" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section features-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Tính năng nổi bật</h2>
            <p class="section-subtitle">Khám phá các công cụ mạnh mẽ giúp bạn tổ chức sự kiện thành công</p>
        </div>

        <div class="features-tabs">
            <ul class="nav nav-tabs justify-content-center" id="featuresTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="planning-tab" data-bs-toggle="tab" data-bs-target="#planning" type="button" role="tab">Lập kế hoạch</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="promotion-tab" data-bs-toggle="tab" data-bs-target="#promotion" type="button" role="tab">Quảng bá</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="management-tab" data-bs-toggle="tab" data-bs-target="#management" type="button" role="tab">Quản lý</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">Phân tích</button>
                </li>
            </ul>

            <div class="tab-content" id="featuresTabsContent">
                <div class="tab-pane fade show active" id="planning" role="tabpanel">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="feature-image">
                                <img src="<?php echo SITE_URL; ?>/assets/images/teamwork.jpg" alt="Event Planning" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h3>Lập kế hoạch sự kiện chuyên nghiệp</h3>
                            <p>Tạo kế hoạch chi tiết cho sự kiện của bạn với các công cụ quản lý thời gian, ngân sách và nguồn lực hiệu quả.</p>
                            <ul class="feature-list">
                                <li>Lịch trình sự kiện thông minh</li>
                                <li>Quản lý ngân sách theo thời gian thực</li>
                                <li>Phân công nhiệm vụ cho đội ngũ</li>
                                <li>Checklist tự động hóa</li>
                            </ul>
                            <a href="<?php echo SITE_URL; ?>/features.php" class="btn btn-primary-custom mt-3">Tìm hiểu thêm</a>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="promotion" role="tabpanel">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="feature-image">
                                <img src="<?php echo SITE_URL; ?>/assets/images/event.jpg" alt="Event Promotion" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h3>Quảng bá sự kiện hiệu quả</h3>
                            <p>Tiếp cận đúng đối tượng với các công cụ marketing tích hợp, từ email marketing đến mạng xã hội.</p>
                            <ul class="feature-list">
                                <li>Trang đăng ký sự kiện tùy chỉnh</li>
                                <li>Chiến dịch email marketing tự động</li>
                                <li>Tích hợp mạng xã hội</li>
                                <li>Mã giảm giá và khuyến mãi</li>
                            </ul>
                            <a href="<?php echo SITE_URL; ?>/features.php" class="btn btn-primary-custom mt-3">Tìm hiểu thêm</a>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="management" role="tabpanel">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="feature-image">
                                <img src="<?php echo SITE_URL; ?>/assets/images/QuanLy.png" alt="Event Management" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h3>Quản lý sự kiện toàn diện</h3>
                            <p>Giám sát mọi khía cạnh của sự kiện từ một nơi, từ đăng ký tham gia đến quản lý tại chỗ.</p>
                            <ul class="feature-list">
                                <li>Quản lý người tham gia thông minh</li>
                                <li>Check-in bằng QR code</li>
                                <li>Ứng dụng di động cho sự kiện</li>
                                <li>Hỗ trợ đa ngôn ngữ</li>
                            </ul>
                            <a href="<?php echo SITE_URL; ?>/features.php" class="btn btn-primary-custom mt-3">Tìm hiểu thêm</a>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="analytics" role="tabpanel">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="feature-image">
                                <img src="<?php echo SITE_URL; ?>/assets/images/phantich.png" alt="Event Analytics" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h3>Phân tích và báo cáo chi tiết</h3>
                            <p>Đo lường thành công của sự kiện với các báo cáo chi tiết và phân tích dữ liệu sâu sắc.</p>
                            <ul class="feature-list">
                                <li>Bảng điều khiển phân tích trực quan</li>
                                <li>Báo cáo ROI sự kiện</li>
                                <li>Phân tích hành vi người tham gia</li>
                                <li>Xu hướng và so sánh sự kiện</li>
                            </ul>
                            <a href="<?php echo SITE_URL; ?>/features.php" class="btn btn-primary-custom mt-3">Tìm hiểu thêm</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section events-section bg-light">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Sự kiện sắp diễn ra</h2>
            <p class="section-subtitle">Khám phá các sự kiện hấp dẫn sắp diễn ra</p>
        </div>

        <div class="row" id="upcomingEvents">
            <?php if (!empty($upcoming_events) && count($upcoming_events) > 0): ?>
                <?php foreach ($upcoming_events as $event_item): ?>
                    <?php
                    $imagePath = !empty($event_item['image'])
                        ? SITE_URL . '/uploads/events/' . htmlspecialchars($event_item['image'])
                        : SITE_URL . '/assets/images/default-event.jpg';
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="event-card-modern">
                            <div class="event-img-container">
                                <img src="<?php echo $imagePath; ?>" class="event-img-modern" alt="<?php echo htmlspecialchars($event_item['title']); ?>">
                                <div class="event-date-badge">
                                    <span class="date-day"><?php echo date('d', strtotime($event_item['date'])); ?></span>
                                    <span class="date-month"><?php echo strtoupper(date('M', strtotime($event_item['date']))); ?></span>
                                </div>
                            </div>
                            <div class="event-body-modern">
                                <div class="event-category text-uppercase text-primary small mb-1">
                                    <?php echo htmlspecialchars($event_item['category_name'] ?? 'Không có danh mục'); ?>
                                </div>
                                <h3 class="event-title-modern"><?php echo htmlspecialchars($event_item['title']); ?></h3>
                                <div class="event-meta mb-2">
                                    <span><i class="bi bi-clock me-1"></i> <?php echo htmlspecialchars($event_item['time']); ?></span>
                                    <span><i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($event_item['location'] ?? 'Chưa cập nhật'); ?></span>
                                </div>
                                <p class="event-description-modern"><?php echo limitText($event_item['description'], 100); ?></p>
                                <div class="event-footer">
                                    <a href="<?php echo SITE_URL; ?>/events/detail.php?id=<?php echo $event_item['id']; ?>" class="btn btn-primary-custom btn-sm">Xem chi tiết</a>
                                    <a href="<?php echo SITE_URL; ?>/events/detail.php?id=<?php echo $event_item['id']; ?>" class="btn btn-outline-primary btn-sm">Đăng ký tham gia</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h4 class="empty-state-title">Không có sự kiện sắp diễn ra</h4>
                        <p class="empty-state-text">Hiện tại không có sự kiện nào sắp diễn ra. Vui lòng quay lại sau.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/events/index.php" class="btn btn-primary-custom btn-lg">Xem tất cả sự kiện</a>
        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container">
        <div class="cta-content text-center">
            <h2 class="cta-title">Sẵn sàng tổ chức sự kiện thành công?</h2>
            <p class="cta-subtitle">Tham gia cùng hàng ngàn nhà tổ chức sự kiện đã tin dùng EventUser</p>
            <div class="cta-buttons">
                <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary-custom btn-lg me-3">Đăng ký miễn phí</a>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline-light btn-lg">Yêu cầu demo</a>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h3 class="footer-title">EventUser</h3>
                <p>Hệ thống quản lý sự kiện toàn diện, giúp bạn tổ chức và tham gia các sự kiện một cách dễ dàng.</p>
                <div class="social-icons mt-3">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h5 class="footer-title">Sản phẩm</h5>
                <ul class="footer-links">
                    <li><a href="<?php echo SITE_URL; ?>/features.php">Tính năng</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pricing.php">Giải pháp</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pricing.php">Bảng giá</a></li>
                    <li><a href="#">Cập nhật</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-4">
                <h5 class="footer-title">Công ty</h5>
                <ul class="footer-links">
                    <li><a href="<?php echo SITE_URL; ?>/about.php">Về chúng tôi</a></li>
                    <li><a href="#">Đối tác</a></li>
                    <li><a href="#">Tuyển dụng</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/contact.php">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-4">
                <h5 class="footer-title">Hỗ trợ</h5>
                <ul class="footer-links">
                    <li><a href="#">Trung tâm trợ giúp</a></li>
                    <li><a href="#">Điều khoản dịch vụ</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-4">
                <h5 class="footer-title">Liên hệ</h5>
                <ul class="footer-links">
                    <li><i class="bi bi-geo-alt me-2"></i> <?php echo ADDRESS; ?></li>
                    <li><i class="bi bi-telephone me-2"></i> <?php echo PHONE; ?></li>
                    <li><i class="bi bi-envelope me-2"></i> <?php echo EMAIL; ?></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?php echo CURRENT_YEAR; ?> EventUser</p>
        </div>
    </div>
</footer>

<?php require_once 'includes/footer.php'; ?>
