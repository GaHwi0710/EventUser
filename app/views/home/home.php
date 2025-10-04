<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$dbi = Database::getInstance();
$pdo = method_exists($dbi,'getConnection') ? $dbi->getConnection() : $dbi;

/* Helpers */
function table_has_columns(PDO $pdo, string $table, array $cols): array {
  $exists = [];
  try {
    $desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $c) { $exists[$c] = in_array($c,$desc,true); }
  } catch(Throwable $e){ foreach($cols as $c){ $exists[$c]=false; } }
  return $exists;
}
$eventsHas = table_has_columns($pdo, 'events', [
  'id','title','description','location','image_url','start_time','end_time','status','created_at'
]);

/* lấy 6 sự kiện mới nhất */
$cols = [];
foreach (['id','title','location','image_url','start_time','end_time'] as $c) {
  if ($eventsHas[$c]) $cols[] = "`$c`";
}
$colSql = $cols ? implode(',', $cols) : "*";
$sql = "SELECT {$colSql} FROM `events` " .
       ($eventsHas['status'] ? "WHERE `status` IN ('active','draft')" : "") .
       " ORDER BY " . ($eventsHas['start_time'] ? "`start_time` DESC" : "`id` DESC") .
       " LIMIT 6";
$events = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];

/* tiện ích định dạng */
function fmt_dt(?string $dt): string {
  if (!$dt) return '';
  $ts = strtotime($dt);
  return $ts ? date('H:i d/m/Y', $ts) : '';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>EvenUser - Trang chủ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<header class="header">
  <div class="container">
    <a href="/public/index.php" class="logo"><span class="logo-icon">EU</span> EvenUser</a>
    <form class="search-container" method="get" action="/app/views/event/list.php">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" class="search-input" name="q" placeholder="Tìm sự kiện, địa điểm...">
        <button class="search-btn" type="submit">Tìm</button>
      </div>
    </form>
    <div class="user-actions">
      <a class="btn-create" href="/app/views/event/add.php"><i class="fa-regular fa-calendar-plus"></i>Tạo sự kiện</a>
      <a class="btn-tickets" href="/app/views/event/list.php"><i class="fa-solid fa-ticket"></i>Danh sách</a>
      <div class="user-profile"><div class="avatar"><i class="fa-regular fa-user"></i></div><span>Account</span></div>
    </div>
  </div>
</header>

<!-- Nav categories (demo tĩnh, JS xử lý active) -->
<nav class="navigation">
  <div class="container">
    <ul class="nav-menu">
      <li class="nav-item active"><a href="#" data-category="all">Tất cả</a></li>
      <li class="nav-item"><a href="#" data-category="music">Âm nhạc</a></li>
      <li class="nav-item"><a href="#" data-category="edu">Học thuật</a></li>
      <li class="nav-item"><a href="#" data-category="tech">Công nghệ</a></li>
      <li class="nav-item"><a href="#" data-category="sport">Thể thao</a></li>
    </ul>
  </div>
</nav>

<main class="container">
  <section class="events-section">
    <div class="events-grid" id="home-events">
      <?php if (empty($events)): ?>
        <!-- Nếu chưa có sự kiện nào -->
        <article class="event-card">
          <div class="card-image-container"><div class="img-placeholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#fff;">Chưa có sự kiện</div></div>
          <div class="card-content">
            <a class="btn-view-details" href="/app/views/event/add.php">Tạo sự kiện đầu tiên</a>
          </div>
        </article>
      <?php else: ?>
        <?php foreach ($events as $ev): 
          $eid   = (int)$ev['id'];
          $title = $eventsHas['title'] ? (string)$ev['title'] : "Sự kiện #$eid";
          $img   = ($eventsHas['image_url'] && !empty($ev['image_url'])) ? (string)$ev['image_url'] : '';
          $loc   = $eventsHas['location'] ? (string)$ev['location'] : '';
          $stTxt = ($eventsHas['start_time'] && !empty($ev['start_time'])) ? fmt_dt($ev['start_time']) : '';
        ?>
        <article class="event-card">
          <a class="card-image-container" href="/app/views/event/detail.php?id=<?php echo $eid; ?>">
            <?php if ($img): ?>
              <img src="/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($title); ?>">
            <?php endif; ?>
          </a>
          <div class="card-content" style="justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
            <div style="color:#ddd;display:flex;flex-direction:column;gap:4px;">
              <div style="font-weight:700;"><?php echo htmlspecialchars($title); ?></div>
              <?php if ($stTxt): ?><div><i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($stTxt); ?></div><?php endif; ?>
              <?php if ($loc): ?><div><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($loc); ?></div><?php endif; ?>
            </div>
            <a class="btn-view-details" href="/app/views/event/detail.php?id=<?php echo $eid; ?>">Xem chi tiết</a>
          </div>
        </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Dots (demo) -->
    <div class="slider-dots">
      <button class="dot active" aria-label="Slide 1"></button>
      <button class="dot" aria-label="Slide 2"></button>
      <button class="dot" aria-label="Slide 3"></button>
    </div>
  </section>
</main>

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

<script src="/assets/js/home.js"></script>
</body>
</html>
