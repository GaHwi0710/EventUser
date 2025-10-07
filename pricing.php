<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

 $pageTitle = "Bảng Giá";
require_once 'includes/header.php';
?>

<section class="pricing-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="pricing-hero">
                    <div class="text-center">
                        <h1 class="display-4 fw-bold text-white mb-4">Bảng Giá Dịch Vụ</h1>
                        <p class="lead text-white">Chọn gói dịch vụ phù hợp nhất với nhu cầu của bạn</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-2">Gói Dịch Vụ</h2>
                <p class="text-center text-muted mb-5">Chúng tôi cung cấp các gói dịch vụ linh hoạt để đáp ứng mọi nhu cầu tổ chức sự kiện</p>
                
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="pricing-card">
                            <div class="pricing-header">
                                <h3 class="pricing-title">Miễn Phí</h3>
                                <div class="pricing-price">
                                    <span class="currency">₫</span>
                                    <span class="amount">0</span>
                                    <span class="period">/tháng</span>
                                </div>
                                <p class="pricing-subtitle">Phù hợp cho cá nhân hoặc sự kiện nhỏ</p>
                            </div>
                            <div class="pricing-body">
                                <ul class="pricing-features">
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Tối đa 3 sự kiện/tháng</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Tối đa 50 người tham gia/sự kiện</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Lưu trữ hình ảnh cơ bản</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Hỗ trợ email</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Báo cáo cơ bản</li>
                                    <li><i class="bi bi-circle text-muted"></i> Tùy chỉnh branding</li>
                                    <li><i class="bi bi-circle text-muted"></i> API truy cập</li>
                                </ul>
                                <div class="pricing-footer">
                                    <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-outline-primary w-100">Đăng Ký Ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="pricing-card popular">
                            <div class="popular-badge">Phổ Biến Nhất</div>
                            <div class="pricing-header">
                                <h3 class="pricing-title">Chuyên Nghiệp</h3>
                                <div class="pricing-price">
                                    <span class="currency">₫</span>
                                    <span class="amount">299,000</span>
                                    <span class="period">/tháng</span>
                                </div>
                                <p class="pricing-subtitle">Phù hợp cho doanh nghiệp vừa và nhỏ</p>
                            </div>
                            <div class="pricing-body">
                                <ul class="pricing-features">
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Tối đa 20 sự kiện/tháng</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Tối đa 300 người tham gia/sự kiện</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Lưu trữ hình ảnh nâng cao</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Hỗ trợ 24/7</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Báo cáo chi tiết</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Tùy chỉnh branding</li>
                                    <li><i class="bi bi-circle text-muted"></i> API truy cập</li>
                                </ul>
                                <div class="pricing-footer">
                                    <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary w-100">Đăng Ký Ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="pricing-card">
                            <div class="pricing-header">
                                <h3 class="pricing-title">Doanh Nghiệp</h3>
                                <div class="pricing-price">
                                    <span class="currency">₫</span>
                                    <span class="amount">999,000</span>
                                    <span class="period">/tháng</span>
                                </div>
                                <p class="pricing-subtitle">Phù hợp cho tập đoàn và tổ chức lớn</p>
                            </div>
                            <div class="pricing-body">
                                <ul class="pricing-features">
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Sự kiện không giới hạn</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Người tham gia không giới hạn</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Lưu trữ không giới hạn</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Hỗ trợ ưu tiên 24/7</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Báo cáo tùy chỉnh</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> Tùy chỉnh branding</li>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> API truy cập đầy đủ</li>
                                </ul>
                                <div class="pricing-footer">
                                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline-primary w-100">Liên Hệ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">So Sánh Các Gói Dịch Vụ</h2>
                <div class="table-responsive">
                    <table class="table table-striped pricing-compare">
                        <thead>
                            <tr>
                                <th>Tính Năng</th>
                                <th>Miễn Phí</th>
                                <th>Chuyên Nghiệp</th>
                                <th>Doanh Nghiệp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Số lượng sự kiện/tháng</td>
                                <td>3</td>
                                <td>20</td>
                                <td><i class="bi bi-infinity"></i> Không giới hạn</td>
                            </tr>
                            <tr>
                                <td>Số người tham gia/sự kiện</td>
                                <td>50</td>
                                <td>300</td>
                                <td><i class="bi bi-infinity"></i> Không giới hạn</td>
                            </tr>
                            <tr>
                                <td>Lưu trữ hình ảnh</td>
                                <td>Cơ bản (1GB)</td>
                                <td>Nâng cao (10GB)</td>
                                <td>Không giới hạn</td>
                            </tr>
                            <tr>
                                <td>Hỗ trợ khách hàng</td>
                                <td>Email</td>
                                <td>24/7</td>
                                <td>Ưu tiên 24/7</td>
                            </tr>
                            <tr>
                                <td>Báo cáo & Thống kê</td>
                                <td>Cơ bản</td>
                                <td>Chi tiết</td>
                                <td>Tùy chỉnh</td>
                            </tr>
                            <tr>
                                <td>Tùy chỉnh branding</td>
                                <td><i class="bi bi-x text-danger"></i></td>
                                <td><i class="bi bi-check text-success"></i></td>
                                <td><i class="bi bi-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>API truy cập</td>
                                <td><i class="bi bi-x text-danger"></i></td>
                                <td><i class="bi bi-x text-danger"></i></td>
                                <td><i class="bi bi-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Tích hợp với các nền tảng khác</td>
                                <td><i class="bi bi-x text-danger"></i></td>
                                <td>Cơ bản</td>
                                <td>Nâng cao</td>
                            </tr>
                            <tr>
                                <td>Xuất dữ liệu</td>
                                <td>Cơ bản</td>
                                <td>PDF, Excel</td>
                                <td>PDF, Excel, CSV</td>
                            </tr>
                            <tr>
                                <td>Quản lý người dùng nâng cao</td>
                                <td><i class="bi bi-x text-danger"></i></td>
                                <td><i class="bi bi-check text-success"></i></td>
                                <td><i class="bi bi-check text-success"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Câu Hỏi Thường Gặp</h2>
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h5>Tôi có thể nâng cấp gói dịch vụ bất cứ lúc nào không?</h5>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Có, bạn có thể nâng cấp gói dịch vụ bất cứ lúc nào. Khi nâng cấp, bạn sẽ chỉ phải trả phần chênh lệch cho phần còn lại của tháng.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h5>Tôi có thể hủy dịch vụ bất cứ lúc nào không?</h5>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Có, bạn có thể hủy dịch vụ bất cứ lúc nào. Không có hợp đồng dài hạn, bạn chỉ trả cho những gì bạn sử dụng.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h5>Có phí thiết lập ban đầu không?</h5>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Không, chúng tôi không thu bất kỳ phí thiết lập ban đầu nào. Bạn chỉ trả tiền theo gói dịch vụ bạn chọn.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h5>Tôi có được dùng thử miễn phí không?</h5>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Có, gói Miễn Phí của chúng tôi luôn miễn phí và không giới hạn thời gian sử dụng. Bạn có thể sử dụng vĩnh viễn mà không phải trả bất kỳ khoản phí nào.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <h2 class="mb-4">Bạn Cần Giải Pháp Tùy Chỉnh?</h2>
                        <p class="lead mb-4">Nếu bạn có nhu cầu đặc biệt hoặc cần một giải pháp được tùy chỉnh riêng cho tổ chức của bạn, hãy liên hệ với chúng tôi để được tư vấn miễn phí.</p>
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary btn-lg">Liên Hệ Tư Vấn</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.pricing-section {
    padding: 80px 0;
    background-color: #f8f9fa;
}

.pricing-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 15px;
    padding: 60px 30px;
    margin-bottom: 40px;
}

.pricing-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.pricing-card.popular {
    position: relative;
    border: 2px solid var(--primary-color);
}

.popular-badge {
    position: absolute;
    top: 15px;
    right: -30px;
    background: var(--primary-color);
    color: white;
    padding: 5px 40px;
    font-size: 0.8rem;
    font-weight: 600;
    transform: rotate(45deg);
}

.pricing-header {
    padding: 30px 20px;
    text-align: center;
    border-bottom: 1px solid #f0f0f0;
}

.pricing-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 15px;
}

.pricing-price {
    margin-bottom: 15px;
}

.currency {
    font-size: 1.2rem;
    vertical-align: top;
    margin-right: 2px;
}

.amount {
    font-size: 3rem;
    font-weight: 700;
    color: var(--primary-color);
}

.period {
    font-size: 1rem;
    color: #6c757d;
}

.pricing-subtitle {
    color: #6c757d;
    font-size: 0.9rem;
}

.pricing-body {
    padding: 30px 20px;
    flex: 1;
}

.pricing-features {
    list-style: none;
    padding: 0;
    margin-bottom: 30px;
}

.pricing-features li {
    margin-bottom: 12px;
    display: flex;
    align-items: center;
}

.pricing-features i {
    margin-right: 10px;
}

.pricing-footer {
    padding: 0 20px 30px;
}

.pricing-compare {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.pricing-compare th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 600;
    text-align: center;
    padding: 15px;
}

.pricing-compare td {
    padding: 15px;
    text-align: center;
}

.pricing-compare tr:nth-child(even) {
    background-color: rgba(0, 0, 0, 0.02);
}

.faq-item {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    margin-bottom: 10px;
}

.faq-question h5 {
    margin: 0;
    color: var(--dark-color);
}

.faq-question i {
    color: var(--primary-color);
    transition: transform 0.3s ease;
}

.faq-answer {
    display: none;
    padding-top: 10px;
    color: #6c757d;
}

.faq-item.active .faq-answer {
    display: block;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const allFaqItems = document.querySelectorAll('.faq-item');
            
            allFaqItems.forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                }
            });
            
            faqItem.classList.toggle('active');
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>