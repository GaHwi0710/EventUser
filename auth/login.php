<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

require_once '../classes/Database.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        setFlash('Vui lòng nhập đầy đủ thông tin', 'danger');
    } else {
        $database = new Database();
        $db = $database->getConnection();

        $user = new User($db);
        $user->email = $username; 
        $user->password = $password;

        $userData = $user->login();

        if ($userData) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['full_name'];
            $_SESSION['user_email'] = $userData['email'];
            $_SESSION['user_role'] = $userData['role'];

            setFlash('Đăng nhập thành công!', 'success');

            if ($userData['role'] === 'admin') {
                redirect(SITE_URL . '/admin/dashboard.php');
            } else {
                redirect(SITE_URL . '/index.php');
            }
        } else {
            setFlash('Tên đăng nhập hoặc mật khẩu không đúng', 'danger');
        }
    }
}

$pageTitle = "Đăng Nhập";
require_once '../includes/header.php';
?>

<section class="login-section">
    <div class="container">
        <div class="auth-container slide-up">
            <div class="auth-header">
                <h2>Đăng Nhập</h2>
                <p>Đăng nhập để truy cập tài khoản của bạn</p>
            </div>

            <div class="auth-body">
                <?php 
                $flash = getFlash();
                if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $flash['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?php echo SITE_URL; ?>/auth/login.php">
                    <div class="form-group">
                        <label for="username" class="form-label">Tên đăng nhập hoặc Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập hoặc email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                        <label class="form-check-label" for="rememberMe">Ghi nhớ đăng nhập</label>
                    </div>

                    <button type="submit" class="btn btn-auth">Đăng Nhập</button>
                </form>

                <div class="social-login">
                    <div class="social-login-title">
                        <span>Hoặc đăng nhập với</span>
                    </div>
                    <div class="social-buttons">
                        <button class="btn btn-social btn-facebook"><i class="bi bi-facebook"></i></button>
                        <button class="btn btn-social btn-google"><i class="bi bi-google"></i></button>
                        <button class="btn btn-social btn-twitter"><i class="bi bi-twitter"></i></button>
                    </div>
                </div>
            </div>

            <div class="auth-footer">
                <p>Bạn chưa có tài khoản? <a href="<?php echo SITE_URL; ?>/auth/register.php">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
