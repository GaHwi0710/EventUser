<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

 $pageTitle = "Tính Năng";
require_once 'includes/header.php';
?>

<section class="features-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="features-hero">
                    <div class="text-center">
                        <h1 class="display-4 fw-bold text-white mb-4">Tính Năng Nổi Bật</h1>
                        <p class="lead text-white">Khám phá các tính năng tuyệt vời của hệ thống quản lý sự kiện EventUser</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Tính Năng Chính</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="text-center mb-4">
                                <div class="feature-icon">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                            </div>
                            <h3 class="text-center mb-3">Quản Lý Sự Kiện</h3>
                            <p>Tạo, chỉnh sửa và quản lý các sự kiện một cách hiệu quả với giao diện trực quan và dễ sử dụng. Hỗ trợ nhiều loại sự kiện khác nhau từ hội thảo, hội nghị đến lễ hội.</p>
                            <ul class="feature-list">
                                <li>Tạo sự kiện nhanh chóng với đầy đủ thông tin</li>
                                <li>Quản lý hình ảnh và mô tả chi tiết</li>
                                <li>Theo dõi trạng thái sự kiện (sắp diễn ra, đang diễn ra, đã kết thúc)</li>
                                <li>Tự động lưu lịch sử thay đổi</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="text-center mb-4">
                                <div class="feature-icon">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                            <h3 class="text-center mb-3">Quản Lý Người Tham Gia</h3>
                            <p>Theo dõi và quản lý danh sách người tham gia sự kiện, gửi thông báo và cập nhật thông tin một cách hiệu quả.</p>
                            <ul class="feature-list">
                                <li>Đăng ký tham gia sự kiện trực tuyến</li>
                                <li>Quản lý trạng thái đăng ký (chờ duyệt, đã duyệt, đã từ chối)</li>
                                <li>Giới hạn số lượng người tham gia</li>
                                <li>Xuất danh sách người tham gia</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="text-center mb-4">
                                <div class="feature-icon">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                            </div>
                            <h3 class="text-center mb-3">Báo Cáo & Thống Kê</h3>
                            <p>Xem báo cáo chi tiết và thống kê về sự kiện, người tham gia và hiệu quả hoạt động để đưa ra quyết định kinh doanh tốt hơn.</p>
                            <ul class="feature-list">
                                <li>Thống kê số lượng sự kiện và người tham gia</li>
                                <li>Báo cáo theo danh mục sự kiện</li>
                                <li>Biểu đồ trực quan về tỷ lệ đăng ký</li>
                                <li>Xuất báo cáo dưới dạng PDF/Excel</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Tính Năng Cho Người Dùng</h2>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="feature-card-alt">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="feature-icon-alt">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>Hồ Sơ Cá Nhân</h4>
                                    <p>Quản lý thông tin cá nhân, cập nhật hồ sơ và theo dõi lịch sử tham gia sự kiện của bạn.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="feature-card-alt">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="feature-icon-alt">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>Sự Kiện Của Tôi</h4>
                                    <p>Xem danh sách các sự kiện bạn đã đăng ký, theo dõi trạng thái và nhận thông báo cập nhật.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="feature-card-alt">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="feature-icon-alt">
                                        <i class="bi bi-search"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>Tìm Kiếm & Lọc</h4>
                                    <p>Tìm kiếm sự kiện theo từ khóa, danh mục hoặc vị trí để nhanh chóng tìm thấy sự kiện phù hợp với bạn.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="feature-card-alt">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="feature-icon-alt">
                                        <i class="bi bi-bell"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>Thông Báo</h4>
                                    <p>Nhận thông báo về trạng thái đăng ký, thay đổi thông tin sự kiện và nhắc nhở sự kiện sắp diễn ra.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Tính Năng Cho Quản Trị Viên</h2>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="feature-card-alt">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="feature-icon-alt">
                                        <i class="bi bi-shield-lock"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>Bảng Điều Khiển</h4>
                                    <p>Giao diện quản lý tập trung với các thống kê quan trọng, giúp quản trị viên theo dõi tình hình hệ thống một cách tổng quan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="feature-card-alt">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="feature-icon-alt">
                                        <i class="bi bi-people"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>Quản Lý Người Dùng</h4>
                                    <p>Quản lý thông tin người dùng, phân quyền và theo dõi hoạt động của người dùng trong hệ thống.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="feature-card-alt">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="feature-icon-alt">
                                        <i class="bi bi-clipboard-check"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>Duyệt Đăng Ký</h4>
                                    <p>Duyệt hoặc từ chối đăng ký tham gia sự kiện, quản lý danh sách người tham gia và gửi thông báo cho người dùng.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="feature-card-alt">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="feature-icon-alt">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h4>Quản Lý Nội Dung</h4>
                                    <p>Tạo, chỉnh sửa và xóa sự kiện, quản lý hình ảnh và thông tin chi tiết của từng sự kiện.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Tính Năng Nâng Cao</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="text-center mb-4">
                                <div class="feature-icon">
                                    <i class="bi bi-globe"></i>
                                </div>
                            </div>
                            <h3 class="text-center mb-3">Đa Ngôn Ngữ</h3>
                            <p>Hỗ trợ đa ngôn ngữ giúp người dùng từ nhiều quốc gia khác nhau có thể sử dụng hệ thống một cách dễ dàng.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="text-center mb-4">
                                <div class="feature-icon">
                                    <i class="bi bi-phone"></i>
                                </div>
                            </div>
                            <h3 class="text-center mb-3">Thân Thiện Di Động</h3>
                            <p>Giao diện responsive hoạt động tốt trên mọi thiết bị, từ máy tính để bàn đến điện thoại thông minh và máy tính bảng.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-card">
                            <div class="text-center mb-4">
                                <div class="feature-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                            </div>
                            <h3 class="text-center mb-3">Bảo Mật</h3>
                            <p>Hệ thống bảo mật cao với mã hóa mật khẩu, xác thực hai lớp và các biện pháp bảo vệ dữ liệu người dùng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <h2 class="mb-4">Bắt Đầu Sử Dụng EventUser Ngay Hôm Nay</h2>
                        <p class="lead mb-4">Đăng ký tài khoản để trải nghiệm đầy đủ các tính năng của hệ thống quản lý sự kiện hàng đầu Việt Nam</p>
                        <div class="d-flex justify-content-center">
                            <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary btn-lg me-3">Đăng Ký Miễn Phí</a>
                            <a href="<?php echo SITE_URL; ?>/events/index.php" class="btn btn-outline-primary btn-lg">Khám Phá Sự Kiện</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.features-section {
    padding: 80px 0;
    background-color: #f8f9fa;
}

.features-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 15px;
    padding: 60px 30px;
    margin-bottom: 40px;
}

.feature-card {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    height: 100%;
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    width: 80px;
    height: 80px;
    background-color: rgba(67, 97, 238, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.feature-icon i {
    font-size: 2.5rem;
    color: var(--primary-color);
}

.feature-card h3 {
    color: var(--dark-color);
    font-weight: 700;
    margin-bottom: 20px;
}

.feature-list {
    list-style: none;
    padding: 0;
}

.feature-list li {
    margin-bottom: 10px;
    padding-left: 25px;
    position: relative;
}

.feature-list li:before {
    content: "\2713";
    position: absolute;
    left: 0;
    color: var(--primary-color);
    font-weight: bold;
}

.feature-card-alt {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    height: 100%;
    transition: all 0.3s ease;
}

.feature-card-alt:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.feature-icon-alt {
    width: 60px;
    height: 60px;
    background-color: rgba(67, 97, 238, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.feature-icon-alt i {
    font-size: 1.8rem;
    color: var(--primary-color);
}

.feature-card-alt h4 {
    color: var(--dark-color);
    font-weight: 600;
    margin-bottom: 10px;
}
</style>

<?php require_once 'includes/footer.php'; ?>