<?php
/***************************************************
 * EvenUser - MY EVENTS (Sự kiện của tôi)
 * - Yêu cầu đăng nhập
 * - Liệt kê sự kiện do chính user tạo (events.created_by = user_id)
 * - Tìm kiếm theo tiêu đề, lọc trạng thái, phân trang
 * - Xoá sự kiện (tuỳ chọn) với CSRF (nếu có quyền & tồn tại)
 ***************************************************/
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (empty($_SESSION['user_id'])) {
  header("Location: /app/views/auth/login.php");
  exit;
}
$uid = (int)$_SESSION['user_id'];

// CSRF helpers
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_verify(?string $t): bool { return isset($_SESSION['csrf']) && is_string($t) && hash_equals($_SESSION['csrf'], $t); }

require_once __DIR__ . '/../../core/Database.php';
$dbi = Database::getInstance();
$pdo = method_exists($dbi,'getConnection') ? $dbi->getConnection() : $dbi;

// helpers
function table_has_columns(PDO $pdo, string $table, array $cols): array {
  $exists = [];
  try { $desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($cols as $c) $exists[$c] = in_array($c,$desc,true);
  } catch(Throwable $e){ foreach ($cols as $c) $exists[$c] = false; }
  return $exists;
}
$eventsHas = table_has_columns($pdo, 'events', [
  'id','title','description','location','image_url','start_time','end_time','status','created_by','created_at'
]);

// Handle delete (optional)
$flash = null; $errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='delete') {
  if (!csrf_verify($_POST['csrf'] ?? null)) {
    $errors[] = "CSRF token không hợp lệ.";
  } else {
    $eid = (int)($_POST['event_id'] ?? 0);
    if ($eid <= 0) {
      $errors[] = "Sự kiện không hợp lệ.";
    } else {
      // chỉ cho xoá nếu là chủ sở hữu
      try {
        $ok = false;
        if ($eventsHas['id'] && $eventsHas['created_by']) {
          $st = $pdo->prepare("SELECT id FROM `events` WHERE id=? AND created_by=? LIMIT 1");
          $st->execute([$eid, $uid]);
          if ($st->fetch()) {
            // soft-delete nếu có cột status, else hard delete
            if ($eventsHas['status']) {
              $pdo->prepare("UPDATE `events` SET status='closed' WHERE id=?")->execute([$eid]);
              $ok = true;
            } else {
              $pdo->prepare("DELETE FROM `events` WHERE id=?")->execute([$eid]);
              $ok = true;
            }
          }
        }
        $flash = $ok ? "Đã xoá sự kiện #$eid." : "Không thể xoá sự kiện.";
      } catch(Throwable $e) {
        $errors[] = "Lỗi xoá: ".$e->getMessage();
      }
    }
  }
}

// filters
$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 8;
$offset = ($page-1)*$limit;

// where
$where = [];
$params = [];

if ($eventsHas['created_by']) { $where[] = "`created_by` = ?"; $params[] = $uid; }
if ($q !== '' && $eventsHas['title']) { $where[] = "`title` LIKE ?"; $params[] = "%$q%"; }
if ($status !== '' && $eventsHas['status']) { $where[] = "`status` = ?"; $params[] = $status; }

$whereSql = $where ? "WHERE ".implode(" AND ", $where) : "";

// total
$stc = $pdo->prepare("SELECT COUNT(*) FROM `events` $whereSql");
$stc->execute($params);
$total = (int)$stc->fetchColumn();

// query
$cols = [];
foreach (['id','title','location','image_url','start_time','end_time','status'] as $c) {
  if ($eventsHas[$c]) $cols[] = "`$c`";
}
$colSql = $cols ? implode(',', $cols) : "*";

$orderBy = $eventsHas['start_time'] ? "`start_time` DESC" : "`id` DESC";

$st = $pdo->prepare("SELECT $colSql FROM `events` $whereSql ORDER BY $orderBy LIMIT $limit OFFSET $offset");
$st->execute($params);
$events = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

$totalPages = (int)ceil(max(1,$total)/$limit);

function fmt_dt(?string $dt): string { if(!$dt) return ''; $ts=strtotime($dt); return $ts?date('H:i d/m/Y',$ts):''; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sự kiện của tôi - EvenUser</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/my-events.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<header class="header">
  <div class="container">
    <a href="/public/index.php" class="logo"><span class="logo-icon">EU</span> EvenUser</a>
    <form id="search-form" class="search-container" method="GET" action="">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" class="search-input" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Tìm sự kiện của bạn...">
        <button class="search-btn" type="submit">Tìm</button>
      </div>
    </form>
    <div class="user-actions">
      <a class="btn-create" href="/app/views/event/add.php"><i class="fa-regular fa-calendar-plus"></i>Tạo sự kiện</a>
      <a class="btn-tickets" href="/app/views/user/profile.php"><i class="fa-solid fa-user"></i>Hồ sơ</a>
      <div class="user-profile"><div class="avatar"><i class="fa-regular fa-user"></i></div><span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span></div>
    </div>
  </div>
</header>

<main class="container page">
  <aside class="filters">
    <h3>Bộ lọc</h3>
    <form id="filter-form" method="GET" action="">
      <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
      <div class="form-group">
        <label for="status">Trạng thái</label>
        <select name="status" id="status">
          <option value="">-- Tất cả --</option>
          <option value="active" <?php echo $status==='active'?'selected':''; ?>>Kích hoạt</option>
          <option value="draft"  <?php echo $status==='draft'?'selected':''; ?>>Nháp</option>
          <option value="closed" <?php echo $status==='closed'?'selected':''; ?>>Đã đóng</option>
        </select>
      </div>
      <div class="filter-actions">
        <button class="btn btn-primary" type="submit">Áp dụng</button>
        <a class="btn btn-secondary" href="?">Xoá lọc</a>
      </div>
    </form>
  </aside>

  <section class="list">
    <div class="list-head">
      <h1>Sự kiện của tôi</h1>
      <a class="btn btn-primary" href="/app/views/event/add.php"><i class="fa-regular fa-calendar-plus"></i> Tạo sự kiện</a>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="alert alert-success"><?php echo $flash; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <strong>Có lỗi:</strong>
        <ul style="margin:6px 0 0 18px"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <?php if (empty($events)): ?>
      <div class="empty-box">
        <i class="fa-regular fa-face-frown"></i>
        <p>Bạn chưa tạo sự kiện nào.</p>
        <a class="btn btn-primary" href="/app/views/event/add.php">Tạo sự kiện đầu tiên</a>
      </div>
    <?php else: ?>
      <div id="my-events" class="event-grid">
        <?php foreach ($events as $ev): 
          $eid   = (int)$ev['id'];
          $title = $eventsHas['title'] ? (string)$ev['title'] : "Sự kiện #$eid";
          $img   = ($eventsHas['image_url'] && !empty($ev['image_url'])) ? (string)$ev['image_url'] : '';
          $loc   = $eventsHas['location'] ? (string)$ev['location'] : '';
          $stTxt = ($eventsHas['start_time'] && !empty($ev['start_time'])) ? fmt_dt($ev['start_time']) : '';
          $statusVal = $eventsHas['status'] ? (string)$ev['status'] : '';
        ?>
        <article class="event-card">
          <a class="thumb" href="/app/views/event/detail.php?id=<?php echo $eid; ?>">
            <?php if ($img): ?>
              <img src="/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($title); ?>">
            <?php else: ?>
              <div class="img-placeholder">EV</div>
            <?php endif; ?>
          </a>
          <div class="body">
            <h3 class="title"><a href="/app/views/event/detail.php?id=<?php echo $eid; ?>"><?php echo htmlspecialchars($title); ?></a></h3>
            <ul class="meta">
              <?php if ($stTxt): ?><li><i class="fa-regular fa-clock"></i><span><?php echo htmlspecialchars($stTxt); ?></span></li><?php endif; ?>
              <?php if ($loc):   ?><li><i class="fa-solid fa-location-dot"></i><span><?php echo htmlspecialchars($loc); ?></span></li><?php endif; ?>
              <?php if ($statusVal!==''): ?><li><i class="fa-solid fa-circle-info"></i><span>Trạng thái: <b class="st-<?php echo htmlspecialchars($statusVal); ?>"><?php echo htmlspecialchars($statusVal); ?></b></span></li><?php endif; ?>
            </ul>

            <div class="actions">
              <a class="btn small" href="/app/views/event/edit.php?id=<?php echo $eid; ?>"><i class="fa-regular fa-pen-to-square"></i> Sửa</a>
              <a class="btn small" href="/app/views/event/detail.php?id=<?php echo $eid; ?>"><i class="fa-solid fa-eye"></i> Xem</a>
              <form class="inline-form" method="POST" action="" onsubmit="return confirm('Xoá sự kiện này?');">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="event_id" value="<?php echo $eid; ?>">
                <button class="btn small danger" type="submit"><i class="fa-regular fa-trash-can"></i> Xoá</button>
              </form>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <?php if ($totalPages > 1): ?>
      <nav class="pagination">
        <?php
          $baseQuery = http_build_query(array_filter(['q'=>$q?:null,'status'=>$status?:null]));
          $mk = fn($p)=>'?'.($baseQuery?$baseQuery.'&':'').'page='.$p;
          $cur=$page;
        ?>
        <a class="page-link <?php echo $cur<=1?'disabled':''; ?>" href="<?php echo $cur<=1?'#':$mk($cur-1); ?>">&laquo;</a>
        <?php for($p=max(1,$cur-2); $p<=min($totalPages,$cur+2); $p++): ?>
          <a class="page-link <?php echo $p==$cur?'active':''; ?>" href="<?php echo $mk($p); ?>"><?php echo $p; ?></a>
        <?php endfor; ?>
        <a class="page-link <?php echo $cur>=$totalPages?'disabled':''; ?>" href="<?php echo $cur>=$totalPages?'#':$mk($cur+1); ?>">&raquo;</a>
      </nav>
      <?php endif; ?>
    <?php endif; ?>
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

<script src="/assets/js/my-events.js"></script>
</body>
</html>
