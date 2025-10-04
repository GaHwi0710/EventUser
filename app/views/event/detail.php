<?php
/***************************************************
 * EvenUser - EVENT DETAIL
 * - Hiển thị chi tiết sự kiện theo ?id=...
 * - Lấy tickets từ bảng event_tickets (nếu có)
 * - Nút "Mua vé" (demo POST với CSRF)
 ***************************************************/
if (session_status() === PHP_SESSION_NONE) { session_start(); }

/* CSRF */
function csrf_token(): string {
  if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
  return $_SESSION['csrf'];
}
function csrf_verify(?string $t): bool {
  return isset($_SESSION['csrf']) && is_string($t) && hash_equals($_SESSION['csrf'], $t);
}

/* DB */
require_once __DIR__ . '/../../core/Database.php';
$dbi = Database::getInstance();
$pdo = method_exists($dbi,'getConnection') ? $dbi->getConnection() : $dbi;

/* Helpers */
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
$eventsHas = table_has_columns($pdo, 'events', [
  'title','description','location','image_url','start_time','end_time','created_by'
]);

/* Input id */
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($eventId <= 0) { http_response_code(400); exit("Thiếu tham số id hợp lệ."); }

/* Load event */
$st = $pdo->prepare("SELECT * FROM `events` WHERE `id`=? LIMIT 1");
$st->execute([$eventId]);
$event = $st->fetch();
if (!$event) { http_response_code(404); exit("Không tìm thấy sự kiện #$eventId"); }

/* Map để hiển thị */
$title       = $eventsHas['title']       ? (string)$event['title']       : "Sự kiện #$eventId";
$desc        = $eventsHas['description'] ? (string)$event['description'] : '';
$location    = $eventsHas['location']    ? (string)$event['location']    : '';
$image_url   = ($eventsHas['image_url'] && !empty($event['image_url'])) ? (string)$event['image_url'] : '';
$start_time  = $eventsHas['start_time']  && !empty($event['start_time']) ? date('H:i d/m/Y', strtotime($event['start_time'])) : '';
$end_time    = $eventsHas['end_time']    && !empty($event['end_time'])   ? date('H:i d/m/Y', strtotime($event['end_time']))   : '';

/* Tickets */
$hasTicketsTable = table_exists($pdo,'event_tickets');
$tickets = [];
if ($hasTicketsTable) {
  $ts = $pdo->prepare("SELECT id,name,price,quantity FROM `event_tickets` WHERE event_id=? ORDER BY id ASC");
  $ts->execute([$eventId]);
  $tickets = $ts->fetchAll() ?: [];
}

/* Xử lý POST mua vé (demo) */
$buySuccess = null; $buyErrors = [];
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='buy') {
  if (!csrf_verify($_POST['csrf'] ?? null)) { $buyErrors[] = "CSRF token không hợp lệ."; }
  $ticket_id = (int)($_POST['ticket_id'] ?? 0);
  $qty       = max(1, (int)($_POST['qty'] ?? 1));

  if (!$tickets) { $buyErrors[] = "Sự kiện này hiện chưa mở bán vé hoặc chưa cấu hình vé."; }
  if ($ticket_id <= 0) { $buyErrors[] = "Vui lòng chọn loại vé."; }

  // Kiểm tra vé tồn tại
  if (!$buyErrors) {
    $found = null;
    foreach ($tickets as $t) { if ((int)$t['id'] === $ticket_id) { $found = $t; break; } }
    if (!$found) { $buyErrors[] = "Loại vé không hợp lệ."; }
    else {
      // Demo: chỉ hiển thị thông báo; bạn có thể redirect sang trang thanh toán/đăng ký
      $total = ((float)$found['price']) * $qty;
      $buySuccess = "Đã chọn mua <strong>{$qty}x {$found['name']}</strong> (Tổng: <strong>".number_format($total,0,'.',',')."đ</strong>).";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($title); ?> - EvenUser</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- CSS chi tiết -->
  <link rel="stylesheet" href="/assets/css/detail.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

  <!-- Header đơn giản -->
  <header class="header">
    <div class="container">
      <a href="/public/index.php" class="logo">
        <span class="logo-icon">EU</span> EvenUser
      </a>
      <div class="search-container">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
          <input type="text" class="search-input" placeholder="Tìm sự kiện, địa điểm...">
          <button class="search-btn">Tìm</button>
        </div>
      </div>
      <div class="user-actions">
        <a class="btn-create" href="/app/views/event/add.php"><i class="fa-regular fa-calendar-plus"></i>Tạo sự kiện</a>
        <a class="btn-tickets" href="#"><i class="fa-solid fa-ticket"></i>Vé của tôi</a>
        <div class="user-profile"><div class="avatar"><i class="fa-regular fa-user"></i></div><span>Account</span></div>
      </div>
    </div>
  </header>

  <!-- Nội dung chi tiết -->
  <main class="container">
    <div class="event-detail-page">

      <!-- Cột trái: thông tin chính -->
      <section class="event-info-main">
        <a class="btn-back" href="javascript:history.back()">
          <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>

        <?php if ($image_url): ?>
          <img class="event-banner-image" src="/<?php echo htmlspecialchars($image_url); ?>" alt="Banner sự kiện">
        <?php else: ?>
          <div class="event-banner-image"></div>
        <?php endif; ?>

        <h1 class="event-title"><?php echo htmlspecialchars($title); ?></h1>

        <div class="organizer-info">
          <div class="organizer-avatar">EV</div>
          <div class="organizer-name">
            <span class="by">Tổ chức bởi</span>
            <span>EvenUser Organizer</span>
          </div>
        </div>

        <div class="event-description">
          <h2>Giới thiệu</h2>
          <?php if ($desc): ?>
            <p><?php echo nl2br(htmlspecialchars($desc)); ?></p>
          <?php else: ?>
            <p>Sự kiện chưa có mô tả chi tiết.</p>
          <?php endif; ?>
        </div>
      </section>

      <!-- Cột phải: sidebar đặt vé -->
      <aside class="event-booking-sidebar">
        <div class="booking-card">
          <div class="booking-card-header">
            <?php
              // lấy min price nếu có ticket
              $minPrice = null;
              foreach ($tickets as $t) {
                $p = (float)$t['price'];
                if ($minPrice===null || $p<$minPrice) $minPrice = $p;
              }
            ?>
            <div class="ticket-price">
              <?php if ($minPrice===null): ?>
                Miễn phí <span>(Chưa cấu hình loại vé)</span>
              <?php elseif ($minPrice==0.0): ?>
                0đ <span>/ Vé</span>
              <?php else: ?>
                <?php echo number_format($minPrice,0,'.',','); ?>đ <span>/ Từ</span>
              <?php endif; ?>
            </div>
          </div>

          <ul class="event-meta-list">
            <?php if ($start_time): ?>
            <li>
              <i class="fa-regular fa-clock"></i>
              <div class="meta-content">
                <span class="label">Thời gian bắt đầu</span>
                <span class="value"><?php echo htmlspecialchars($start_time); ?></span>
              </div>
            </li>
            <?php endif; ?>

            <?php if ($end_time): ?>
            <li>
              <i class="fa-regular fa-hourglass-end"></i>
              <div class="meta-content">
                <span class="label">Thời gian kết thúc</span>
                <span class="value"><?php echo htmlspecialchars($end_time); ?></span>
              </div>
            </li>
            <?php endif; ?>

            <?php if ($location): ?>
            <li>
              <i class="fa-solid fa-location-dot"></i>
              <div class="meta-content">
                <span class="label">Địa điểm</span>
                <span class="value"><?php echo htmlspecialchars($location); ?></span>
              </div>
            </li>
            <?php endif; ?>

            <li>
              <i class="fa-solid fa-calendar-check"></i>
              <div class="meta-content">
                <span class="label">Mã sự kiện</span>
                <span class="value">#<?php echo (int)$eventId; ?></span>
              </div>
            </li>
          </ul>

          <div style="padding: 0 20px 20px;">
            <?php if (!empty($buySuccess)): ?>
              <div class="alert alert-success" style="margin-bottom:10px;"><?php echo $buySuccess; ?></div>
            <?php endif; ?>
            <?php if (!empty($buyErrors)): ?>
              <div class="alert alert-error" style="margin-bottom:10px;">
                <strong>Có lỗi:</strong>
                <ul style="margin:6px 0 0 18px;">
                  <?php foreach ($buyErrors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if ($tickets): ?>
              <form method="POST" style="display:flex; flex-direction:column; gap:12px;">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <input type="hidden" name="action" value="buy">
                <label for="ticket_id" style="font-weight:600;">Chọn loại vé</label>
                <select id="ticket_id" name="ticket_id" required>
                  <option value="">-- Chọn --</option>
                  <?php foreach ($tickets as $t): ?>
                    <option value="<?php echo (int)$t['id']; ?>">
                      <?php echo htmlspecialchars($t['name']); ?> - <?php echo number_format((float)$t['price'],0,'.',','); ?>đ (SL: <?php echo (int)$t['quantity']; ?>)
                    </option>
                  <?php endforeach; ?>
                </select>

                <label for="qty" style="font-weight:600;">Số lượng</label>
                <input id="qty" name="qty" type="number" min="1" value="1" required>

                <button class="btn-buy-tickets" type="submit"><i class="fa-solid fa-cart-shopping"></i> Mua vé</button>
              </form>
            <?php else: ?>
              <button class="btn-buy-tickets" type="button" disabled>Chưa mở bán vé</button>
            <?php endif; ?>
          </div>
        </div>
      </aside>

    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h4>Về EvenUser</h4>
          <p>Nền tảng quản lý & khám phá sự kiện hiện đại dành cho sinh viên.</p>
        </div>
        <div class="footer-section">
          <h4>Liên kết nhanh</h4>
          <ul>
            <li><a href="#">Trang chủ</a></li>
            <li><a href="/app/views/event/add.php">Tạo sự kiện</a></li>
            <li><a href="#">Vé của tôi</a></li>
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
    <script src="/assets/js/detail.js"></script>
</body>
</html>
