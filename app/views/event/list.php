<?php
/***************************************************
 * EvenUser - EVENT LIST (danh sách sự kiện)
 * - Tìm kiếm theo từ khóa (?q=)
 * - Lọc theo khoảng thời gian (?from=YYYY-mm-dd&to=YYYY-mm-dd)
 * - Phân trang (?page=1,2,..) – mặc định 9 sự kiện / trang
 * - Hiển thị giá "Từ ..." dựa trên bảng event_tickets (nếu có)
 ***************************************************/
if (session_status() === PHP_SESSION_NONE) { session_start(); }

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
  'id','title','description','location','image_url','start_time','end_time','status','created_at'
]);
$hasTicketsTable = table_exists($pdo,'event_tickets');

/* Input filters */
$q     = trim($_GET['q'] ?? '');
$from  = trim($_GET['from'] ?? ''); // YYYY-mm-dd
$to    = trim($_GET['to'] ?? '');   // YYYY-mm-dd
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 9;
$offset= ($page - 1) * $limit;

/* Build WHERE */
$where = [];
$params = [];

if ($q !== '' && $eventsHas['title']) {
  $where[] = "`title` LIKE ?";
  $params[] = "%$q%";
}
if ($from !== '' && $eventsHas['start_time']) {
  $where[] = "`start_time` >= ?";
  $params[] = $from . " 00:00:00";
}
if ($to !== '' && $eventsHas['end_time']) {
  $where[] = "`end_time` <= ?";
  $params[] = $to . " 23:59:59";
}
// ưu tiên chỉ hiển thị 'active' nếu có cột status
if ($eventsHas['status']) {
  $where[] = "`status` IN ('active','draft')"; // hiển thị active + draft (tùy bạn)
}
$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

/* Count total */
$countSql = "SELECT COUNT(*) FROM `events` {$whereSql}";
$stc = $pdo->prepare($countSql);
$stc->execute($params);
$total = (int)$stc->fetchColumn();

/* Query page */
$cols = [];
foreach (['id','title','description','location','image_url','start_time','end_time'] as $c) {
  if ($eventsHas[$c]) $cols[] = "`$c`";
}
$colSql = $cols ? implode(',', $cols) : "*";
$sql = "SELECT {$colSql} FROM `events` {$whereSql} ORDER BY " . ($eventsHas['start_time'] ? "`start_time` DESC" : "`id` DESC") . " LIMIT {$limit} OFFSET {$offset}";
$st = $pdo->prepare($sql);
$st->execute($params);
$events = $st->fetchAll() ?: [];

/* Helper: get min ticket price for event */
function get_min_price(PDO $pdo, int $eventId): ?float {
  try {
    $st = $pdo->prepare("SELECT MIN(price) FROM `event_tickets` WHERE event_id=?");
    $st->execute([$eventId]);
    $v = $st->fetchColumn();
    if ($v === null || $v === false) return null;
    return (float)$v;
  } catch(Throwable $e) { return null; }
}

$totalPages = (int)ceil(max(1, $total) / $limit);
$page = min(max(1, $page), max(1, $totalPages));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sự kiện - EvenUser</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/list.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<header class="header">
  <div class="container">
    <a href="/public/index.php" class="logo"><span class="logo-icon">EU</span> EvenUser</a>
    <form id="search-form" class="search-container" method="GET" action="">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" class="search-input" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Tìm sự kiện...">
        <button class="search-btn" type="submit">Tìm</button>
      </div>
    </form>
    <div class="user-actions">
      <a class="btn-create" href="/app/views/event/add.php"><i class="fa-regular fa-calendar-plus"></i>Tạo sự kiện</a>
      <a class="btn-tickets" href="#"><i class="fa-solid fa-ticket"></i>Vé của tôi</a>
      <div class="user-profile"><div class="avatar"><i class="fa-regular fa-user"></i></div><span>Account</span></div>
    </div>
  </div>
</header>

<main class="container">
  <div class="list-page">

    <aside class="filters">
      <h3>Bộ lọc</h3>
      <form id="filter-form" method="GET" action="">
        <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
        <div class="form-group">
          <label for="from">Từ ngày</label>
          <input type="date" id="from" name="from" value="<?php echo htmlspecialchars($from); ?>">
        </div>
        <div class="form-group">
          <label for="to">Đến ngày</label>
          <input type="date" id="to" name="to" value="<?php echo htmlspecialchars($to); ?>">
        </div>

        <div class="filter-actions">
          <button class="btn btn-primary" type="submit">Áp dụng</button>
          <a class="btn btn-secondary" href="?">Xoá lọc</a>
        </div>

        <div class="view-toggle">
          <button type="button" class="btn small" data-view="grid" id="btn-grid"><i class="fa-solid fa-border-all"></i></button>
          <button type="button" class="btn small" data-view="list" id="btn-list"><i class="fa-solid fa-list"></i></button>
        </div>
      </form>
    </aside>

    <section class="results">
      <div class="results-header">
        <h1>Sự kiện</h1>
        <div class="meta"><?php echo (int)$total; ?> kết quả</div>
      </div>

      <?php if (empty($events)): ?>
        <div class="empty-box">
          <i class="fa-regular fa-face-frown"></i>
          <p>Không tìm thấy sự kiện phù hợp.</p>
          <a class="btn btn-primary" href="/app/views/event/add.php"><i class="fa-regular fa-calendar-plus"></i> Tạo sự kiện đầu tiên</a>
        </div>
      <?php else: ?>
        <div id="event-list" class="event-grid">
          <?php foreach ($events as $ev): 
            $eid   = (int)$ev['id'];
            $title = $eventsHas['title'] ? (string)$ev['title'] : "Sự kiện #$eid";
            $img   = ($eventsHas['image_url'] && !empty($ev['image_url'])) ? (string)$ev['image_url'] : '';
            $loc   = $eventsHas['location'] ? (string)$ev['location'] : '';
            $stTxt = ($eventsHas['start_time'] && !empty($ev['start_time'])) ? date('H:i d/m/Y', strtotime($ev['start_time'])) : '';
            $enTxt = ($eventsHas['end_time']   && !empty($ev['end_time']))   ? date('H:i d/m/Y', strtotime($ev['end_time']))   : '';
            $minPrice = $hasTicketsTable ? get_min_price($pdo, $eid) : null;
          ?>
          <article class="event-card">
            <a class="thumb" href="/app/views/event/detail.php?id=<?php echo $eid; ?>">
              <?php if ($img): ?>
                <img src="/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($title); ?>">
              <?php else: ?>
                <div class="img-placeholder">EV</div>
              <?php endif; ?>
            </a>
            <div class="event-body">
              <h3 class="event-title">
                <a href="/app/views/event/detail.php?id=<?php echo $eid; ?>"><?php echo htmlspecialchars($title); ?></a>
              </h3>

              <ul class="meta">
                <?php if ($stTxt): ?>
                <li><i class="fa-regular fa-clock"></i> <span><?php echo htmlspecialchars($stTxt); ?></span></li>
                <?php endif; ?>
                <?php if ($enTxt): ?>
                <li><i class="fa-regular fa-hourglass-end"></i> <span><?php echo htmlspecialchars($enTxt); ?></span></li>
                <?php endif; ?>
                <?php if ($loc): ?>
                <li><i class="fa-solid fa-location-dot"></i> <span><?php echo htmlspecialchars($loc); ?></span></li>
                <?php endif; ?>
              </ul>

              <div class="card-footer">
                <div class="price">
                  <?php
                    if ($minPrice === null) echo '<span class="free">Miễn phí / N.A</span>';
                    elseif ($minPrice == 0.0) echo '<span class="free">Miễn phí</span>';
                    else echo '<span class="value">'.number_format($minPrice,0,'.',',')."đ</span> <span class='sub'>/ Từ</span>";
                  ?>
                </div>
                <a class="btn btn-primary small" href="/app/views/event/detail.php?id=<?php echo $eid; ?>">
                  <i class="fa-solid fa-ticket"></i> Xem chi tiết
                </a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination">
          <?php
            // giữ lại q, from, to
            $baseQuery = http_build_query(array_filter([
              'q' => $q ?: null,
              'from' => $from ?: null,
              'to' => $to ?: null,
            ]));
            $mk = function($p) use ($baseQuery) {
              return '?'. ($baseQuery ? $baseQuery.'&' : '') . 'page='.$p;
            };
          ?>
          <a class="page-link <?php echo $page<=1?'disabled':''; ?>" href="<?php echo $page<=1 ? '#' : $mk($page-1); ?>">&laquo;</a>
          <?php
            // hiển thị window nhỏ quanh current
            $start = max(1, $page-2);
            $end   = min($totalPages, $page+2);
            for ($p=$start; $p<=$end; $p++):
          ?>
            <a class="page-link <?php echo $p==$page?'active':''; ?>" href="<?php echo $mk($p); ?>"><?php echo $p; ?></a>
          <?php endfor; ?>
          <a class="page-link <?php echo $page>=$totalPages?'disabled':''; ?>" href="<?php echo $page>=$totalPages ? '#' : $mk($page+1); ?>">&raquo;</a>
        </nav>
        <?php endif; ?>

      <?php endif; ?>
    </section>

  </div>
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
          <li><a href="/app/views/event/add.php">Tạo sự kiện</a></li>
          <li><a href="/app/views/event/list.php">Danh sách sự kiện</a></li>
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

<script src="/assets/js/list.js"></script>
</body>
</html>
