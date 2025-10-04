<?php
/***************************************************
 * EvenUser - REGISTER
 * - Thuần PHP + PDO (Database singleton)
 * - Đăng ký bằng Email hoặc SĐT + Mật khẩu
 * - Kiểm tra trùng, băm mật khẩu, CSRF
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

// table helper
function table_has_columns(PDO $pdo, string $table, array $cols): array {
  $exists = [];
  try {
    $desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $c) { $exists[$c] = in_array($c,$desc,true); }
  } catch(Throwable $e){ foreach($cols as $c){ $exists[$c]=false; } }
  return $exists;
}
$usersHas = table_has_columns($pdo, 'users', ['id','email','phone','password_hash','name','role','created_at']);

// Handle POST
$errors = []; $success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['csrf'] ?? null)) {
    $errors[] = "CSRF token không hợp lệ.";
  } else {
    $email_or_phone = trim($_POST['email'] ?? '');
    $password       = (string)($_POST['password'] ?? '');
    $confirm        = (string)($_POST['confirm_password'] ?? '');
    $name           = trim($_POST['name'] ?? '');

    // validate
    if ($email_or_phone === '') $errors[] = "Vui lòng nhập Email hoặc số điện thoại.";
    if ($password === '')       $errors[] = "Vui lòng nhập Mật khẩu.";
    if (strlen($password) < 6)  $errors[] = "Mật khẩu phải có tối thiểu 6 ký tự.";
    if ($confirm === '')        $errors[] = "Vui lòng nhập lại Mật khẩu.";
    if ($confirm !== $password) $errors[] = "Mật khẩu nhập lại không khớp.";

    // basic detect email or phone
    $isEmail = filter_var($email_or_phone, FILTER_VALIDATE_EMAIL) !== false;
    $isPhone = !$isEmail && preg_match('/^(0|\+84)\d{9,10}$/', $email_or_phone);

    if (!$isEmail && !$isPhone) {
      $errors[] = "Email hoặc số điện thoại không hợp lệ.";
    }

    if (!$errors) {
      // uniqueness check
      $where = []; $params = [];
      if ($isEmail && $usersHas['email']) { $where[] = '`email` = ?'; $params[] = $email_or_phone; }
      if ($isPhone && $usersHas['phone']) { $where[] = '`phone` = ?'; $params[] = $email_or_phone; }

      if (!$where) {
        $errors[] = "Bảng users thiếu cột email/phone.";
      } else {
        $sql = "SELECT COUNT(*) FROM `users` WHERE " . implode(' OR ', $where);
        $st = $pdo->prepare($sql);
        $st->execute($params);
        $exists = (int)$st->fetchColumn() > 0;
        if ($exists) {
          $errors[] = "Tài khoản đã tồn tại.";
        } else {
          // insert
          $hash = password_hash($password, PASSWORD_DEFAULT);
          $cols = ['password_hash'];
          $vals = [$hash];
          if ($isEmail && $usersHas['email']) { $cols[]='email'; $vals[]=$email_or_phone; }
          if ($isPhone && $usersHas['phone']) { $cols[]='phone'; $vals[]=$email_or_phone; }
          if ($usersHas['name']) { $cols[]='name'; $vals[] = $name !== '' ? $name : 'User'; }
          if ($usersHas['role']) { $cols[]='role'; $vals[] = 'user'; }
          if ($usersHas['created_at']) { $cols[]='created_at'; $vals[] = date('Y-m-d H:i:s'); }

          $colSql = '`' . implode('`,`', $cols) . '`';
          $qm     = rtrim(str_repeat('?,', count($cols)), ',');
          $ins = $pdo->prepare("INSERT INTO `users` ($colSql) VALUES ($qm)");
          $ins->execute($vals);

          $success = "Tạo tài khoản thành công! Bạn có thể đăng nhập ngay.";
          // auto login? tuỳ chọn – ở đây chuyển sang login
          header("Location: /app/views/auth/login.php");
          exit;
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
  <title>Đăng ký - EvenUser</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/register.css">
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h1>Tạo tài khoản EvenUser</h1>
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
        <label for="name">Họ và tên (tuỳ chọn)</label>
        <input type="text" id="name" name="name" placeholder="Nguyễn Văn A" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
      </div>

      <div class="form-group">
        <label for="email">Email hoặc Số điện thoại</label>
        <input type="text" id="email" name="email" placeholder="vd: student@uni.edu hoặc 0989xxx..." data-error-target="err-email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <small id="err-email" style="color:#c0392b"></small>
      </div>

      <div class="form-group">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" placeholder="••••••••" data-error-target="err-password">
        <small class="password-hint">Tối thiểu 6 ký tự; nên có chữ hoa/thường & số.</small>
        <small id="err-password" style="color:#c0392b"></small>
      </div>

      <div class="form-group">
        <label for="confirm_password">Nhập lại mật khẩu</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" data-error-target="err-confirm">
        <small id="err-confirm" style="color:#c0392b"></small>
      </div>

      <button class="login-btn" type="submit">Tiếp tục</button>
    </form>

    <div class="login-divider"><span>hoặc</span></div>

    <div class="login-links">
      <span class="link-text">Đã có tài khoản?</span>
      <a class="link register-link" href="/app/views/auth/login.php">Đăng nhập ngay</a>
    </div>
  </div>

  <script src="/assets/js/register.js"></script>
</body>
</html>
