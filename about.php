<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = "Về EventUser";
require_once 'includes/header.php';
?>

<section class="about-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="about-hero text-center">
                    <h1 class="display-4 fw-bold text-white mb-4">Về EventUser</h1>
                    <p class="lead text-white">Nền tảng quản lý sự kiện hàng đầu tại Việt Nam</p>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-6">
                <div class="about-content">
                    <h2 class="mb-4">Câu Chuyện Của Chúng Tôi</h2>
                    <p>EventUser được thành lập vào năm 2025 với sứ mệnh mang đến một giải pháp quản lý sự kiện toàn diện và hiệu quả cho các tổ chức, doanh nghiệp và cá nhân tại Việt Nam.</p>
                    <p>Xuất phát từ nhu cầu thực tế về một nền tảng công nghệ giúp đơn giản hóa quy trình tổ chức sự kiện, EventUser đã không ngừng phát triển và cải tiến để trở thành hệ thống quản lý sự kiện đáng tin cậy nhất hiện nay.</p>
                    <p>Trải qua nhiều giai đoạn phát triển, EventUser đã phục vụ hàng trăm sự kiện lớn nhỏ trên khắp cả nước, từ hội thảo chuyên ngành đến lễ hội văn hóa, từ workshop đào tạo đến hội chợ thương mại.</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="about-image">
                    <img src="assets/images/about.png" class="img-fluid rounded shadow" alt="Về EventUser">
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Tầm Nhìn & Sứ Mệnh</h2>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <i class="bi bi-eye-fill text-primary" style="font-size: 3rem;"></i>
                                <h4 class="mt-3">Tầm Nhìn</h4>
                                <p>Trở thành nền tảng quản lý sự kiện hàng đầu tại Việt Nam, nơi mọi sự kiện đều được tổ chức một cách chuyên nghiệp, hiệu quả và mang lại trải nghiệm tuyệt vời cho người tham gia.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <i class="bi bi-bullseye text-success" style="font-size: 3rem;"></i>
                                <h4 class="mt-3">Sứ Mệnh</h4>
                                <p>Cung cấp giải pháp công nghệ đột phá giúp khách hàng tổ chức sự kiện thành công, tối ưu hóa quy trình và nâng cao trải nghiệm người tham gia thông qua nền tảng quản lý sự kiện thông minh và thân thiện.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Giá Trị Cốt Lõi</h2>
                <div class="row text-center">
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-lightbulb-fill text-warning" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3">Sáng Tạo</h5>
                                <p>Luôn tìm kiếm giải pháp mới mẻ và sáng tạo để giải quyết mọi thách thức trong quản lý sự kiện.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-shield-check-fill text-success" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3">Minh Bạch</h5>
                                <p>Hoạt động với sự minh bạch, trung thực và trách nhiệm với khách hàng và đối tác.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-people-fill text-primary" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3">Hợp Tác</h5>
                                <p>Đặt sự hợp tác và làm việc nhóm lên hàng đầu để tạo ra giá trị tốt nhất cho khách hàng.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-graph-up-arrow text-danger" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3">Phát Triển</h5>
                                <p>Không ngừng học hỏi và phát triển để nâng cao chất lượng dịch vụ và sản phẩm.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Đội Ngũ Của Chúng Tôi</h2>
                <div class="row">
                    <?php
                    $members = [
                        ["Kiều Gia Huy", "Giám Đốc Điều Hành", "Với hơn 10 năm kinh nghiệm trong lĩnh vực công nghệ và quản lý sự kiện, anh Huy là người dẫn dắt EventUser đến thành công hiện tại."],
                        ["Khúc Trí Bằng", "Trưởng Phát Triển Sản Phẩm", "Anh Bằng chịu trách nhiệm phát triển và cải tiến sản phẩm, đảm bảo EventUser luôn đáp ứng nhu cầu ngày càng cao của khách hàng."],
                        ["Nguyễn Đình Tiền Hải", "Trưởng Kinh Doanh", "Anh Hải có kinh nghiệm rộng trong lĩnh vực kinh doanh và tiếp thị, giúp EventUser mở rộng thị trường và xây dựng mối quan hệ bền vững."]
                    ];
                    foreach ($members as $m):
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <div class="card-body">
                                <img src="https://ssl.gstatic.com/accounts/ui/avatar_2x.png" 
                                     class="rounded-circle mb-3 border"
                                     width="150" height="150" alt="Default Avatar">
                                <h5 class="card-title fw-bold"><?php echo $m[0]; ?></h5>
                                <p class="text-muted"><?php echo $m[1]; ?></p>
                                <p><?php echo $m[2]; ?></p>
                                <div class="social-icons mt-3">
                                    <a href="#" class="text-primary"><i class="bi bi-linkedin"></i></a>
                                    <a href="#" class="text-primary"><i class="bi bi-twitter"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-4">Liên Hệ Với Chúng Tôi</h2>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="contact-item">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-geo-alt-fill text-danger me-3" style="font-size: 1.5rem;"></i>
                                        <h5>Địa Chỉ</h5>
                                    </div>
                                    <p><?php echo ADDRESS; ?></p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="contact-item">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-telephone-fill text-primary me-3" style="font-size: 1.5rem;"></i>
                                        <h5>Điện Thoại</h5>
                                    </div>
                                    <p><?php echo PHONE; ?></p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="contact-item">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-envelope-fill text-success me-3" style="font-size: 1.5rem;"></i>
                                        <h5>Email</h5>
                                    </div>
                                    <p><?php echo EMAIL; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.about-section {
    padding: 80px 0;
    background-color: #f8f9fa;
}
.about-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 15px;
    padding: 60px 30px;
    margin-bottom: 40px;
}
.about-content {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}
.contact-item {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}
.card-body img {
    background-color: #f9f9f9;
    padding: 4px;
}
</style>

<?php require_once 'includes/footer.php'; ?>
