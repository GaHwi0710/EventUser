<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ✅ Khai báo an toàn để tránh "Undefined array key"
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($full_name) || empty($email) || empty($password)) {
        setFlash('Vui lòng nhập đầy đủ thông tin', 'danger');
    } elseif ($password != $confirmPassword) {
        setFlash('Mật khẩu và xác nhận mật khẩu không khớp', 'danger');
    } else {
        require_once '../classes/Database.php';
        require_once '../classes/User.php';
        
        $database = new Database();
        $db = $database->getConnection();
        
        $user = new User($db);
        $user->full_name = $full_name;
        $user->email = $email;
        $user->password = $password;
        $user->phone = '';
        $user->role = 'user';
        
        if ($user->userExists()) {
            setFlash('Email đã được sử dụng', 'danger');
        } else {
            if ($user->register()) {
                setFlash('Đăng ký thành công! Vui lòng đăng nhập', 'success');
                redirect(SITE_URL . '/auth/login.php');
            } else {
                setFlash('Đăng ký thất bại, vui lòng thử lại', 'danger');
            }
        }
    }
}

$pageTitle = "Đăng Ký";
require_once '../includes/header.php';
?>

<section class="register-section">
    <div class="container">
        <div class="auth-container slide-up">
            <div class="auth-header">
                <h2>Đăng Ký Tài Khoản</h2>
                <p>Tạo tài khoản mới để bắt đầu sử dụng dịch vụ</p>
            </div>
            <div class="auth-body">
                <?php 
                $flash = getFlash();
                if ($flash): 
                ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <form method="post" action="<?php echo SITE_URL; ?>/auth/register.php">
                    <!-- Họ và tên -->
                    <div class="form-group">
                        <label for="full_name" class="form-label">Họ và tên</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nhập họ và tên của bạn" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required>
                        </div>
                    </div>

                    <!-- Mật khẩu -->
                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-meter" id="passwordStrengthMeter"></div>
                        </div>
                        <div class="password-strength-text" id="passwordStrengthText"></div>
                    </div>

                    <!-- Xác nhận mật khẩu -->
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Xác nhận lại mật khẩu" required>
                        </div>
                    </div>

                    <!-- Điều khoản -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" name="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-auth">Đăng Ký</button>
                </form>
            </div>
            <div class="auth-footer">
                <p>Bạn đã có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/login.php">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
