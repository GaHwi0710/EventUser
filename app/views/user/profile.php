<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Redirect nếu chưa đăng nhập
if (empty($_SESSION['user_id'])) {
  header("Location: /app/views/auth/login.php");
  exit;
}

// CSRF helpers
function csrf_token(): string {
  if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
  return $_SESSION['csrf'];
}
function csrf_verify(?string $t): bool {
  return isset($_SESSION['csrf']) && is_string($t) && hash_equals($_SESSION['csrf'], $t);
}

// DB
$dbi = Database::getInstance();
$pdo = method_exists($dbi,'getConnection') ? $dbi->getConnection() : $dbi;

// helpers
function table_exists(PDO $pdo, string $table): bool {
  try { $pdo->query("SELECT 1 FROM `$table` LIMIT 1"); return true; } catch(Throwable $e){ return false; }
}
function table_has_columns(PDO $pdo, string $table, array $cols): array {
  $exists = [];
  try {
    $desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $c) { $exists[$c] = in_array($c,$desc,true); }
  } catch(Throwable $e){ foreach($cols as $c){ $exists[$c]=false; } }
  return $exists;
}

$uid = (int)$_SESSION['user_id'];

// users
$usersHas = table_has_columns($pdo, 'users', ['id','email','phone','password_hash','name','role','created_at']);

// Lấy thông tin user
$user = null;
if ($usersHas['id']) {
  $st = $pdo->prepare("SELECT * FROM `users` WHERE `id`=? LIMIT 1");
  $st->execute([$uid]);
  $user = $st->fetch(PDO::FETCH_ASSOC);
}

// Cập nhật thông tin cơ bản (name, phone) - tuỳ chọn
$profileSuccess = null; $profileErrors = [];
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='update_profile') {
  if (!csrf_verify($_POST['csrf'] ?? null)) {
    $profileErrors[] = "CSRF token không hợp lệ.";
  } else {
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validate phone nếu nhập
    if ($phone !== '' && !preg_match('/^(0|\\+84)\\d{9,10}$/', $phone)) {
      $profileErrors[] = "Số điện thoại không hợp lệ.";
    }

    if (!$profileErrors) {
      try {
        $sets = []; $vals = [];
        if ($usersHas['name']) { $sets[] = "`name`=?";  $vals[] = ($name !== '' ? $name : ($user['name'] ?? '')); }
        if ($usersHas['phone']){ $sets[] = "`phone`=?"; $vals[] = $phone; }

        if ($sets) {
          $vals[] = $uid;
          $sql = "UPDATE `users` SET ".implode(',', $sets)." WHERE `id`=?";
          $pdo->prepare($sql)->execute($vals);
          $profileSuccess = "Cập nhật thông tin thành công!";
          // cập nhật session hiển thị
          if (!empty($name)) $_SESSION['user_name'] = $name;
          // reload user
          $st = $pdo->prepare("SELECT * FROM `users` WHERE `id`=? LIMIT 1");
          $st->execute([$uid]);
          $user = $st->fetch(PDO::FETCH_ASSOC);
        }
      } catch(Throwable $e) {
        $profileErrors[] = "Lỗi cập nhật: ".$e->getMessage();
      }
    }
  }
}

// Lấy danh sách vé đã mua/đăng ký
$hasReg  = table_exists($pdo,'registrations');
$hasTick = table_exists($pdo,'event_tickets');
$hasEv   = table_exists($pdo,'events');

$tickets = [];
if ($hasReg && $hasEv) {
  $sql = "SELECT r.id as reg_id, r.quantity, r.created_at,
                 e.id as event_id, e.title, e.location, e.start_time, e.image_url,
                 t.name as ticket_name, t.price as ticket_price
          FROM `registrations` r
          JOIN `events` e ON r.event_id = e.id
          LEFT JOIN `event_tickets` t ON r.ticket_id = t.id
          WHERE r.user_id = ?
          ORDER BY r.created_at DESC
          LIMIT 50";
  $st = $pdo->prepare($sql);
  $st->execute([$uid]);
  $tickets = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

// tiện ích
function fmt(?string $dt): string { if(!$dt) return ''; $ts=strtotime($dt); return $ts?date('H:i d/m/Y',$ts):''; }
function money_vn($n): string { $n = (float)$n; return number_format($n,0,'.',',').'đ'; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Hồ sơ cá nhân - EvenUser</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<header class="header">
  <div class="container-header">
    <a href="/public/index.php" class="logo"><span class="logo-icon">EU</span> EvenUser</a>
    <form class="search-container" action="/app/views/event/list.php" method="get">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" class="search-input" name="q" placeholder="Tìm sự kiện, địa điểm...">
        <button class="search-btn" type="submit">Tìm</button>
      </div>
    </form>
    <div class="user-actions">
      <a class="btn-create" href="/app/views/event/add.php"><i class="fa-regular fa-calendar-plus"></i>Tạo sự kiện</a>
      <a class="btn-tickets" href="/app/views/event/list.php"><i class="fa-solid fa-ticket"></i>Danh sách</a>
      <div class="user-profile"><div class="avatar"><i class="fa-regular fa-user"></i></div><span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span></div>
    </div>
  </div>
</header>

<main class="container-main">
  <h1 class="page-title">Tài khoản của tôi</h1>

  <!-- Tabs -->
  <ul class="account-tabs">
    <li class="tab-link active" data-tab="tab-info">Thông tin tài khoản</li>
    <li class="tab-link" data-tab="tab-tickets">Vé của tôi</li>
  </ul>

  <!-- Tab 1: Info -->
  <div id="tab-info" class="tab-pane active">
    <?php if (!empty($profileSuccess)): ?>
      <div style="background:#e8f7ee;color:#11633a;border:1px solid #bfe6cd;padding:10px 12px;border-radius:8px;margin-bottom:14px">
        <?php echo $profileSuccess; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($profileErrors)): ?>
      <div style="background:#fdecec;color:#8b2a2a;border:1px solid #f5c2c2;padding:10px 12px;border-radius:8px;margin-bottom:14px">
        <strong>Có lỗi:</strong>
        <ul style="margin:6px 0 0 18px">
          <?php foreach($profileErrors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST" action="" novalidate>
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <input type="hidden" name="action" value="update_profile">

        <div class="form-group">
          <label>Họ và tên</label>
          <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="text" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
        </div>

        <div class="form-group">
          <label>Số điện thoại</label>
          <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
        </div>

        <button class="btn-primary" type="submit">Lưu thay đổi</button>
      </form>
    </div>
  </div>

  <!-- Tab 2: Tickets -->
  <div id="tab-tickets" class="tab-pane">
    <?php if (empty($tickets)): ?>
      <div class="form-card">
        <p>Bạn chưa có vé nào.</p>
        <p><a class="btn-primary" href="/app/views/event/list.php">Khám phá sự kiện</a></p>
      </div>
    <?php else: ?>
      <div class="tickets-list">
        <?php foreach($tickets as $t): 
          $eid = (int)($t['event_id'] ?? 0);
          $img = (string)($t['image_url'] ?? '');
          $title = (string)($t['title'] ?? ('Sự kiện #'.$eid));
          $st = fmt($t['start_time'] ?? null);
          $loc = (string)($t['location'] ?? '');
          $ticketName = (string)($t['ticket_name'] ?? 'Vé');
          $price = isset($t['ticket_price']) ? money_vn($t['ticket_price']) : '0đ';
          $qty = (int)($t['quantity'] ?? 1);
          $sum = isset($t['ticket_price']) ? money_vn(((float)$t['ticket_price']) * $qty) : '0đ';
        ?>
        <div class="ticket-card">
          <div class="ticket-image">
            <?php if ($img): ?>
            <img src="/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($title); ?>">
            <?php endif; ?>
          </div>
          <div class="ticket-info">
            <h3><?php echo htmlspecialchars($title); ?></h3>
            <ul class="ticket-meta">
              <?php if ($st): ?><li><i class="fa-regular fa-clock"></i><?php echo htmlspecialchars($st); ?></li><?php endif; ?>
              <?php if ($loc): ?><li><i class="fa-solid fa-location-dot"></i><?php echo htmlspecialchars($loc); ?></li><?php endif; ?>
              <li><i class="fa-solid fa-ticket"></i><?php echo htmlspecialchars($ticketName); ?> × <?php echo (int)$qty; ?> (<?php echo $price; ?>)</li>
              <li><i class="fa-solid fa-coins"></i>Tổng: <?php echo $sum; ?></li>
            </ul>
            <div class="ticket-actions">
              <a class="btn-view-ticket" href="/app/views/event/detail.php?id=<?php echo $eid; ?>">Xem sự kiện</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</main>

<footer class="footer">
  <div class="container-footer">
    <div class="footer-content">
      <div class="footer-section">
        <h4>Về EvenUser</h4>
        <p>Nền tảng quản lý & khám phá sự kiện hiện đại dành cho sinh viên.</p>
      </div>
      <div class="footer-section">
        <h4>Liên kết nhanh</h4>
        <ul>
          <li><a href="/public/index.php">Trang chủ</a></li>
          <li><a href="/app/views/event/list.php">Danh sách sự kiện</a></li>
          <li><a href="/app/views/event/add.php">Tạo sự kiện</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Kết nối</h4>
        <div class="social-links">
          <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
          <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">© <?php echo date('Y'); ?> EvenUser. All rights reserved.</div>
  </div>
</footer>

<script src="/assets/js/profile.js"></script>
</body>
</html>
