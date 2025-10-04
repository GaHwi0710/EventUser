<?php
// app/controllers/AuthController.php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    protected function renderView(string $view, array $vars = []): void {
        $base = dirname(__DIR__) . '/views';
        extract($vars, EXTR_SKIP);
        include $base . '/layouts/header.php';
        include $base . '/' . ltrim($view, '/');
        include $base . '/layouts/footer.php';
    }

    public function login(): void {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $account  = trim($_POST['email'] ?? '');
            $password = (string)($_POST['password'] ?? '');

            if ($account === '')  $errors[] = 'Vui lòng nhập Email hoặc SĐT.';
            if ($password === '') $errors[] = 'Vui lòng nhập Mật khẩu.';

            if (!$errors) {
                $user = (new User())->findByEmailOrPhone($account);
                if (!$user || !(new User())->verifyPassword($user, $password)) {
                    $errors[] = 'Thông tin đăng nhập không đúng.';
                } else {
                    Auth::login($user);
                    header('Location: /public/index.php?r=home/index');
                    exit;
                }
            }
        }

        $this->renderView('auth/login.php', ['errors' => $errors]);
    }

    public function register(): void {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = trim($_POST['name'] ?? '');
            $acc     = trim($_POST['email'] ?? '');
            $pass    = (string)($_POST['password'] ?? '');
            $confirm = (string)($_POST['confirm_password'] ?? '');

            $isEmail = filter_var($acc, FILTER_VALIDATE_EMAIL) !== false;
            $isPhone = !$isEmail && preg_match('/^(0|\+84)\d{9,10}$/', $acc);

            if (!$isEmail && !$isPhone)        $errors[] = 'Email hoặc SĐT không hợp lệ.';
            if (strlen($pass) < 6)              $errors[] = 'Mật khẩu tối thiểu 6 ký tự.';
            if ($pass !== $confirm)             $errors[] = 'Mật khẩu nhập lại không khớp.';

            if (!$errors) {
                $userModel = new User();
                if ($isEmail && $userModel->existsEmail($acc)) $errors[] = 'Email đã tồn tại.';
                if ($isPhone && $userModel->existsPhone($acc)) $errors[] = 'SĐT đã tồn tại.';

                if (!$errors) {
                    $data = [
                        'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
                        'name'          => $name,
                        'role'          => 'user'
                    ];
                    if ($isEmail) $data['email'] = $acc;
                    if ($isPhone) $data['phone'] = $acc;

                    $userModel->create($data);
                    header('Location: /public/index.php?r=auth/login');
                    exit;
                }
            }
        }

        $this->renderView('auth/register.php', ['errors' => $errors]);
    }

    public function logout(): void {
        Auth::logout();
        header('Location: /public/index.php?r=home/index');
        exit;
    }
}
