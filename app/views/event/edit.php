<?php
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
$dbi = Database::getInstance();
$pdo = method_exists($dbi,'getConnection') ? $dbi->getConnection() : $dbi;

/* Schema helper */
function table_has_columns(PDO $pdo, string $table, array $cols): array {
  $exists = [];
  try {
    $desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $c) { $exists[$c] = in_array($c,$desc,true); }
  } catch(Throwable $e){ foreach($cols as $c){ $exists[$c]=false; } }
  return $exists;
}
$eventsHas = table_has_columns($pdo,'events',['title','description','location','image_url']);

/* Input id */
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($eventId<=0) { http_response_code(400); exit("Thiếu tham số id hợp lệ."); }

/* Load row */
$st = $pdo->prepare("SELECT * FROM `events` WHERE `id`=? LIMIT 1");
$st->execute([$eventId]);
$row = $st->fetch();
if (!$row) { http_response_code(404); exit("Không tìm thấy sự kiện #$eventId"); }

/* Map ra giao diện */
$page_title = "Admin - Chỉnh sửa sự kiện";
$event_data = [
  "id"          => $row['id'],
  "name"        => $eventsHas['title']       ? (string)$row['title']       : '',
  "venue"       => '',
  "province"    => '',
  "ward"        => '',
  "address"     => $eventsHas['location']    ? (string)$row['location']    : '',
  "description" => $eventsHas['description'] ? (string)$row['description'] : '',
  "banner_url"  => ($eventsHas['image_url'] && !empty($row['image_url'])) ? (string)$row['image_url'] : ''
];

/* Submit */
$errors=[]; $success=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!csrf_verify($_POST['csrf'] ?? null)) $errors[]="CSRF token không hợp lệ.";

  $event_name   = trim($_POST['event_name']   ?? '');
  $venue_name   = trim($_POST['venue_name']   ?? '');
  $province     = trim($_POST['province']     ?? '');
  $ward         = trim($_POST['ward']         ?? '');
  $street       = trim($_POST['street_address'] ?? '');
  $description  = trim($_POST['event_description'] ?? '');

  if ($event_name==='')  $errors[]="Vui lòng nhập Tên sự kiện.";
  if ($description==='') $errors[]="Vui lòng nhập Mô tả sự kiện.";

  $composedLocation = trim(
    ($venue_name!=='' ? $venue_name.' - ' : '') .
    ($street!=='' ? $street.', ' : '') .
    ($ward!=='' ? $ward.', ' : '') .
    ($province!=='' ? $province : '')
  , " -,\t\n\r\0\x0B");

  // upload banner (tuỳ chọn)
  $new_banner_url = null;
  if (!empty($_FILES['event_banner']['name'])) {
    $f = $_FILES['event_banner'];
    if ($f['error'] === UPLOAD_ERR_OK) {
      $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
      if (!in_array($ext,['jpg','jpeg','png'], true)) {
        $errors[] = "Ảnh banner không hợp lệ (chỉ JPG/PNG).";
      } elseif ($f['size'] > 5*1024*1024) {
        $errors[] = "Ảnh banner vượt quá 5MB.";
      } else {
        $uploads = __DIR__ . '/../../../assets/images/events';
        if (!is_dir($uploads)) { @mkdir($uploads,0777,true); }
        if (!is_dir($uploads)) $errors[]="Không tạo được thư mục upload.";
        else {
          $newName = 'ev_'.date('Ymd_His').'_' . bin2hex(random_bytes(4)).'.'.$ext;
          $dest = rtrim($uploads,'/\\').DIRECTORY_SEPARATOR.$newName;
          if (@move_uploaded_file($f['tmp_name'],$dest)) {
            $new_banner_url = 'assets/images/events/'.$newName;
          } else { $errors[] = "Upload ảnh thất bại."; }
        }
      }
    } elseif ($f['error']!==UPLOAD_ERR_NO_FILE) {
      $errors[] = "Lỗi upload ảnh (mã: {$f['error']}).";
    }
  }

  if (!$errors) {
    try {
      $sets=[]; $vals=[];
      if ($eventsHas['title'])       { $sets[]='`title`=?';       $vals[]=$event_name; }
      if ($eventsHas['description']) { $sets[]='`description`=?'; $vals[]=$description; }
      if ($eventsHas['location'])    { $sets[]='`location`=?';    $vals[]=$composedLocation; }
      if ($eventsHas['image_url'] && $new_banner_url!==null) { $sets[]='`image_url`=?'; $vals[]=$new_banner_url; }

      if ($sets) {
        $vals[]=$eventId;
        $sql="UPDATE `events` SET ".implode(', ',$sets)." WHERE `id`=?";
        $pdo->prepare($sql)->execute($vals);
      }

      // cập nhật biến để hiển thị lại form
      $event_data['name']        = $event_name;
      $event_data['venue']       = $venue_name;
      $event_data['province']    = $province;
      $event_data['ward']        = $ward;
      $event_data['address']     = $street;
      $event_data['description'] = $description;
      if ($new_banner_url!==null) $event_data['banner_url'] = $new_banner_url;

      $success = "Cập nhật sự kiện thành công!";
    } catch(Throwable $e) {
      $errors[] = "Lỗi cập nhật: ".$e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/assets/css/edit.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
  <div class="organizer-wrapper">
    <aside class="sidebar">
      <div class="sidebar-header"><h1>Event Admin Center</h1></div>
      <nav class="sidebar-nav">
        <a href="#" class="nav-item"><i class="fa-solid fa-calendar-plus"></i><span>Thêm sự kiện</span></a>
        <a href="#" class="nav-item active"><i class="fa-solid fa-list-check"></i><span>Chỉnh sửa sự kiện</span></a>
      </nav>
    </aside>

    <main class="main-content">
      <header class="main-header">
        <div class="header-actions">
          <div class="user-menu"><i class="fa-solid fa-user-shield"></i><span>Admin</span><i class="fa-solid fa-chevron-down"></i></div>
        </div>
      </header>

      <div class="content-area">
        <div class="content-header">
          <h1>Chỉnh sửa sự kiện #<?php echo (int)$event_data['id']; ?></h1>
          <div class="action-buttons">
            <button type="submit" form="edit-event-form" class="btn btn-primary">Cập nhật</button>
          </div>
        </div>

        <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-error">
            <strong>Có lỗi:</strong>
            <ul style="margin:6px 0 0 18px;">
              <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form class="add-event-form" id="edit-event-form" method="POST" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">

          <div class="form-card">
            <div class="form-group">
              <label for="event-name" class="required">Tên sự kiện</label>
              <input type="text" id="event-name" name="event_name" value="<?php echo htmlspecialchars($event_data['name']); ?>">
            </div>
            <div class="form-group">
              <label for="venue-name" class="required">Tên địa điểm</label>
              <input type="text" id="venue-name" name="venue_name" value="<?php echo htmlspecialchars($event_data['venue']); ?>">
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="province" class="required">Tỉnh/Thành</label>
                <select id="province" name="province" value="<?php echo htmlspecialchars($event_data['province']); ?>">
                  <option value="">Chọn Tỉnh/Thành</option>
                  <option value="hanoi" <?php echo $event_data['province']==='hanoi'?'selected':''; ?>>Hà Nội</option>
                  <option value="hcm"   <?php echo $event_data['province']==='hcm'?'selected':''; ?>>TP. Hồ Chí Minh</option>
                  <option value="dn"    <?php echo $event_data['province']==='dn'?'selected':''; ?>>Đà Nẵng</option>
                </select>
              </div>
              <div class="form-group">
                <label for="ward" class="required">Xã/Phường</label>
                <select id="ward" name="ward" data-selected-ward="<?php echo htmlspecialchars($event_data['ward']); ?>">
                  <option value="">-- Chọn Tỉnh/Thành trước --</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="street-address">Địa chỉ cụ thể (Số nhà, tên đường)</label>
              <input type="text" id="street-address" name="street_address" value="<?php echo htmlspecialchars($event_data['address']); ?>">
            </div>
          </div>

          <div class="form-card">
            <div class="form-group">
              <label for="event-description" class="required">Mô tả sự kiện</label>
              <textarea id="event-description" name="event_description" rows="12"><?php echo htmlspecialchars($event_data['description']); ?></textarea>
            </div>

            <div class="form-group">
              <label for="event-banner" class="required">Ảnh bìa (Banner)</label>
              <div class="file-upload-wrapper">
                <input type="file" id="event-banner" name="event_banner" class="file-input" accept=".jpg,.jpeg,.png">
                <div class="file-upload-placeholder" style="<?php echo !empty($event_data['banner_url']) ? 'display:none;' : ''; ?>">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Kéo và thả file hoặc <span>chọn file</span></p>
                  <small>Hỗ trợ JPG, PNG (tối đa 5MB)</small>
                </div>
                <div class="file-preview-container" id="banner-preview-container" style="<?php echo empty($event_data['banner_url']) ? 'display:none;' : 'display:block;'; ?>">
                  <img id="banner-preview" src="<?php echo htmlspecialchars($event_data['banner_url']); ?>" alt="Xem trước banner">
                  <button type="button" class="btn-remove-preview" id="remove-banner-preview">&times;</button>
                </div>
              </div>
            </div>
          </div>
        </form>

      </div>
    </main>
  </div>

  <script src="/assets/js/edit.js"></script>
</body>
</html>