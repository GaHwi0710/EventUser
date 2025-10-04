<?php
/***************************************************
 * EvenUser - LOGIN
 * - Thuần PHP + PDO (Database singleton)
 * - Email hoặc SĐT + Mật khẩu (hash verify)
 * - CSRF + Thông báo lỗi thân thiện
 ***************************************************/
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// CSRF helpers
function csrf_token(): string {
  if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
  return $_SESSION['csrf'];
}
function csrf_verify(?string $t): bool {
  return isset($_SESSION['csrf']) && is_string($t) && hash_equals($_SESSION['csrf'], $t);
}

// DB
require_once __DIR__ . '/../../core/Database.php';
$dbi = Database::getInstance();
$pdo = method_exists($dbi,'getConnection') ? $dbi->getConnection() : $dbi;

// check users table columns
function table_has_columns(PDO $pdo, string $table, array $cols): array {
  $exists = [];
  try {
    $desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $c) { $exists[$c] = in_array($c,$desc,true); }
  } catch(Throwable $e){ foreach($cols as $c){ $exists[$c]=false; } }
  return $exists;
}
$usersHas = table_has_columns($pdo, 'users', ['id','email','phone','password_hash','name','role']);

// Handle POST
$errors = []; $success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['csrf'] ?? null)) {
    $errors[] = "CSRF token không hợp lệ.";
  } else {
    $email_or_phone = trim($_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';

    if ($email_or_phone === '') $errors[] = "Vui lòng nhập Email hoặc SĐT.";
    if ($password === '')       $errors[] = "Vui lòng nhập Mật khẩu.";

    if (!$errors) {
      // Build WHERE (email or phone)
      $where = []; $params = [];
      if ($usersHas['email']) { $where[] = '`email` = ?'; $params[] = $email_or_phone; }
      if ($usersHas['phone']) { $where[] = '`phone` = ?'; $params[] = $email_or_phone; }
      if (!$where) {
        $errors[] = "Bảng users chưa đúng cấu trúc (thiếu email/phone).";
      } else {
        $sql = "SELECT * FROM `users` WHERE (" . implode(' OR ', $where) . ") LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->execute($params);
        $user = $st->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
          $errors[] = "Tài khoản không tồn tại.";
        } else {
          // verify password (hash)
          $hash = $usersHas['password_hash'] ? $user['password_hash'] : null;
          $ok = $hash ? password_verify($password, (string)$hash) : false;
          if ($ok) {
            // set session
            $_SESSION['user_id']   = (int)$user['id'];
            $_SESSION['user_name'] = $usersHas['name'] && !empty($user['name']) ? (string)$user['name'] : 'User';
            $_SESSION['user_role'] = $usersHas['role'] && !empty($user['role']) ? (string)$user['role'] : 'user';

            // Regenerate
            if (function_exists('session_regenerate_id')) { @session_regenerate_id(true); }

            // Redirect về danh sách
            header("Location: /app/views/event/list.php");
            exit;
          } else {
            $errors[] = "Mật khẩu không đúng.";
          }
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập - EvenUser</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h1>Đăng nhập EvenUser</h1>
    </div>

    <?php if (!empty($errors)): ?>
      <div style="background:#fdecec;color:#8b2a2a;border:1px solid #f5c2c2;padding:10px 12px;border-radius:8px;margin-bottom:14px">
        <strong>Có lỗi:</strong>
        <ul style="margin:6px 0 0 18px">
          <?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form class="login-form" method="POST" action="" novalidate>
      <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <div class="form-group">
        <label for="email">Email hoặc Số điện thoại</label>
        <input type="text" id="email" name="email" placeholder="vd: student@uni.edu hoặc 0989xxx..." data-error-target="err-email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <small id="err-email" style="color:#c0392b"></small>
      </div>
      <div class="form-group">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" placeholder="••••••••" data-error-target="err-password">
        <small id="err-password" style="color:#c0392b"></small>
      </div>
      <button class="login-btn" type="submit">Tiếp tục</button>
    </form>

    <div class="login-divider"><span>hoặc</span></div>

    <div class="login-links">
      <a class="link" href="#">Quên mật khẩu?</a>
      <a class="link register-link" href="/app/views/auth/register.php">Tạo tài khoản mới</a>
    </div>
  </div>

  <script src="/assets/js/login.js"></script>
</body>
</html>
