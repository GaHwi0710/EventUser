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

/* Kết nối Database: theo cấu trúc đã chốt */
$dbi = Database::getInstance();
$pdo = method_exists($dbi,'getConnection') ? $dbi->getConnection() : $dbi;

/* Tạm thời cho phép nếu chưa có hệ thống auth */
$created_by = $_SESSION['user_id'] ?? 1;

/* Helpers kiểm tra schema */
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
  'image_url','status','payment_method','capacity','start_time','end_time','location','description','title','created_by'
]);
$hasTicketsTable = table_exists($pdo, 'event_tickets');

/* Submit */
$errors=[]; $success=null; $image_url=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!csrf_verify($_POST['csrf']??null)) { $errors[]='CSRF token không hợp lệ.'; }

  $title       = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $location    = trim($_POST['location'] ?? '');
  $start_time  = $_POST['start_time'] ?? '';
  $end_time    = $_POST['end_time'] ?? '';
  $capacity    = (int)($_POST['capacity'] ?? 100);

  $ticket_name  = $_POST['ticket_name']  ?? [];
  $ticket_price = $_POST['ticket_price'] ?? [];
  $ticket_qty   = $_POST['ticket_qty']   ?? [];

  $payment_method = $_POST['payment_method'] ?? 'cash';
  $status         = $_POST['status'] ?? 'active';

  if ($title==='') $errors[]='Vui lòng nhập Tiêu đề.';
  if ($description==='') $errors[]='Vui lòng nhập Mô tả.';
  if ($location==='') $errors[]='Vui lòng nhập Địa điểm.';
  if ($start_time==='') $errors[]='Vui lòng chọn Bắt đầu.';
  if ($end_time==='') $errors[]='Vui lòng chọn Kết thúc.';
  if ($start_time && $end_time) {
    $st=strtotime($start_time); $et=strtotime($end_time);
    if ($st===false || $et===false) $errors[]='Thời gian không hợp lệ.';
    elseif ($et<=$st) $errors[]='Kết thúc phải sau Bắt đầu.';
  }
  if ($capacity<1) $errors[]='Capacity phải ≥ 1.';

  /* Upload ảnh (nếu có) */
  if (!empty($_FILES['image']['name'])) {
    $f=$_FILES['image'];
    if ($f['error']===UPLOAD_ERR_OK) {
      $ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
      if (!in_array($ext,['jpg','jpeg','png','gif','webp'],true)) {
        $errors[]='Ảnh không hợp lệ.';
      } else {
        // uploads vào /assets/images/events (root project)
        $uploadsDir = __DIR__ . '/../../../assets/images/events';
        if (!is_dir($uploadsDir)) { @mkdir($uploadsDir,0777,true); }
        if (!is_dir($uploadsDir)) { $errors[]='Không tạo được thư mục upload ảnh.'; }
        else {
          $new='ev_'.date('Ymd_His').'_' . bin2hex(random_bytes(4)).'.'.$ext;
          $dest=rtrim($uploadsDir,'/\\').DIRECTORY_SEPARATOR.$new;
          if (@move_uploaded_file($f['tmp_name'],$dest)) {
            $image_url = 'assets/images/events/'.$new;
          } else { $errors[]='Upload ảnh thất bại.'; }
        }
      }
    } elseif ($f['error']!==UPLOAD_ERR_NO_FILE) { $errors[]='Lỗi upload ảnh.'; }
  }

  if (!$errors) {
    try{
      $pdo->beginTransaction();
      $cols=['title','description','location','start_time','end_time','capacity','created_by'];
      $vals=[$title,$description,$location,$start_time,$end_time,$capacity,$created_by];
      if ($eventsHas['image_url'] && $image_url!==null){ $cols[]='image_url'; $vals[]=$image_url; }
      if ($eventsHas['status']){ $cols[]='status'; $vals[]=$status; }
      if ($eventsHas['payment_method']){ $cols[]='payment_method'; $vals[]=$payment_method; }

      $colSql='`'.implode('`,`',$cols).'`';
      $qm=rtrim(str_repeat('?,',count($cols)),',');
      $pdo->prepare("INSERT INTO `events` ($colSql) VALUES ($qm)")->execute($vals);
      $event_id=(int)$pdo->lastInsertId();

      if ($hasTicketsTable && is_array($ticket_name)) {
        $ins=$pdo->prepare("INSERT INTO `event_tickets`(event_id,name,price,quantity) VALUES (?,?,?,?)");
        for($i=0;$i<count($ticket_name);$i++){
          $name=trim($ticket_name[$i]??''); $price=(float)($ticket_price[$i]??0); $qty=(int)($ticket_qty[$i]??0);
          if ($name!==''){ $ins->execute([$event_id,$name,$price,$qty]); }
        }
      }

      $pdo->commit();
      $success="Đã tạo sự kiện <strong>".htmlspecialchars($title)."</strong> thành công!";
      $_POST=[]; $_FILES=[]; $image_url=null;
    }catch(Throwable $e){
      if($pdo->inTransaction()) $pdo->rollBack();
      $errors[]='Lỗi ghi dữ liệu: '.$e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Thêm sự kiện - EventUser</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- LIÊN KẾT CSS GỘP -->
  <link rel="stylesheet" href="/assets/css/add.css">
</head>
<body class="page-information-event">
  <div class="organizer-wrapper">
    <aside class="sidebar">
      <div class="sidebar-header"><h1>EventUser</h1></div>
      <nav class="sidebar-nav">
        <a class="nav-item active" href="#">Tạo sự kiện</a>
        <a class="nav-item" href="#">Danh sách</a>
      </nav>
    </aside>

    <main class="main-content">
      <header class="main-header">
        <div class="header-actions">
          <button class="btn btn-primary" type="submit" form="add-form">Lưu nhanh</button>
        </div>
      </header>

      <section class="content-area">
        <div class="content-header">
          <div class="progress-steps">
            <div class="step active"><div class="step-circle">1</div><div class="step-label">Thông tin</div></div>
            <div class="step-connector"></div>
            <div class="step"><div class="step-circle">2</div><div class="step-label">Loại vé</div></div>
            <div class="step-connector"></div>
            <div class="step"><div class="step-circle">3</div><div class="step-label">Thanh toán</div></div>
            <div class="step-connector"></div>
            <div class="step"><div class="step-circle">4</div><div class="step-label">Cài đặt</div></div>
          </div>
          <div class="action-buttons">
            <a href="javascript:history.back()" class="btn btn-secondary">Quay lại</a>
          </div>
        </div>

        <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-error">
            <strong>Có lỗi:</strong>
            <ul style="margin:6px 0 0 18px;">
              <?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form class="add-event-form" id="add-form" method="POST" action="" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">

          <!-- 1) THÔNG TIN -->
          <div class="form-card">
            <h2>1) Thông tin sự kiện</h2>
            <div class="form-row">
              <div class="form-group">
                <label class="required">Tiêu đề</label>
                <input type="text" name="title" id="event-title" maxlength="120" value="<?php echo htmlspecialchars($_POST['title']??''); ?>" required>
              </div>
              <div class="form-group">
                <label class="required">Địa điểm</label>
                <input type="text" name="location" id="street-address" maxlength="160" value="<?php echo htmlspecialchars($_POST['location']??''); ?>" required>
                <div class="char-counter" id="address-counter"></div>
              </div>
            </div>

            <div class="form-group">
              <label class="required">Mô tả</label>
              <textarea name="description" id="event-description" maxlength="2000" required><?php echo htmlspecialchars($_POST['description']??''); ?></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="required">Bắt đầu</label>
                <input type="datetime-local" name="start_time" value="<?php echo htmlspecialchars($_POST['start_time']??''); ?>" required>
              </div>
              <div class="form-group">
                <label class="required">Kết thúc</label>
                <input type="datetime-local" name="end_time" value="<?php echo htmlspecialchars($_POST['end_time']??''); ?>" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="required">Sức chứa (capacity)</label>
                <input type="number" name="capacity" min="1" value="<?php echo htmlspecialchars($_POST['capacity']??'100'); ?>" required>
              </div>
              <div class="form-group">
                <label>Ảnh bìa (tuỳ chọn)</label>
                <div class="file-upload-wrapper">
                  <input type="file" class="file-input" name="image" id="event-banner" accept=".jpg,.jpeg,.png,.gif,.webp">
                  <div class="file-upload-placeholder">
                    <i>📁</i><p>Kéo & thả hoặc <span>chọn ảnh</span></p><small>PNG, JPG, GIF, WEBP</small>
                  </div>
                </div>
                <div class="file-preview-container" id="banner-preview-container" style="display:none;">
                  <img id="banner-preview" alt="">
                  <button type="button" class="btn-remove-preview" id="remove-banner-preview">×</button>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Tỉnh/Thành</label>
                <select id="province">
                  <option value="">-- Chọn Tỉnh/Thành --</option>
                  <option value="hanoi">Hà Nội</option>
                  <option value="hcm">TP.HCM</option>
                  <option value="dn">Đà Nẵng</option>
                </select>
              </div>
              <div class="form-group">
                <label>Xã/Phường</label>
                <select id="ward" disabled>
                  <option value="">-- Chọn Tỉnh/Thành trước --</option>
                </select>
              </div>
            </div>
          </div>

          <!-- 2) LOẠI VÉ -->
          <div class="form-card">
            <h2>2) Loại vé</h2>
            <div id="ticket-box">
              <div class="form-row">
                <div class="form-group">
                  <label>Tên loại vé</label>
                  <input type="text" id="ticket-name" name="ticket_name[]" maxlength="80" placeholder="Vé thường / VIP" value="<?php echo isset($_POST['ticket_name'][0])?htmlspecialchars($_POST['ticket_name'][0]):''; ?>">
                  <div class="char-counter" id="ticket-name-counter"></div>
                </div>
                <div class="form-group">
                  <label>Giá (VNĐ)</label>
                  <input type="number" id="ticket-price" name="ticket_price[]" min="0" step="1000" value="<?php echo isset($_POST['ticket_price'][0])?htmlspecialchars($_POST['ticket_price'][0]):'0'; ?>">
                </div>
                <div class="form-group">
                  <label>Số lượng</label>
                  <input type="number" name="ticket_qty[]" min="0" value="<?php echo isset($_POST['ticket_qty'][0])?htmlspecialchars($_POST['ticket_qty'][0]):'0'; ?>">
                </div>
              </div>
              <label style="display:flex; gap:8px; align-items:center; margin-top:8px;">
                <input type="checkbox" id="free-ticket-checkbox"> Miễn phí (tự đặt giá = 0 & khoá input giá)
              </label>
            </div>
            <div style="margin-top:10px;">
              <button type="button" class="btn btn-secondary" id="btn-add-ticket-row">+ Thêm loại vé</button>
            </div>
          </div>

          <!-- 3) THANH TOÁN -->
          <div class="form-card">
            <h2>3) Thanh toán</h2>
            <div class="form-row">
              <div class="form-group">
                <label>Phương thức thanh toán</label>
                <?php $pm=$_POST['payment_method']??'cash'; ?>
                <select name="payment_method" id="payment-method">
                  <option value="cash" <?php echo $pm==='cash'?'selected':''; ?>>Tiền mặt</option>
                  <option value="card" <?php echo $pm==='card'?'selected':''; ?>>Thẻ ngân hàng</option>
                  <option value="momo" <?php echo $pm==='momo'?'selected':''; ?>>Ví MoMo</option>
                  <option value="bank" <?php echo $pm==='bank'?'selected':''; ?>>Chuyển khoản</option>
                </select>
              </div>
              <div class="form-group">
                <label>Tên chủ TK</label>
                <input type="text" id="account-holder" maxlength="80">
                <div class="char-counter" id="account-holder-counter"></div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Số tài khoản</label>
                <input type="text" id="account-number" maxlength="30">
                <div class="char-counter" id="account-number-counter"></div>
              </div>
              <div class="form-group">
                <label>Ngân hàng</label>
                <input type="text" id="bank-name" maxlength="80">
                <div class="char-counter" id="bank-name-counter"></div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Chi nhánh</label>
                <input type="text" id="branch-name" maxlength="80">
                <div class="char-counter" id="branch-name-counter"></div>
              </div>
              <div class="form-group">
                <label>Họ tên người nhận</label>
                <input type="text" id="full-name" maxlength="80">
                <div class="char-counter" id="full-name-counter"></div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Địa chỉ xuất hoá đơn</label>
                <input type="text" id="address" maxlength="120">
                <div class="char-counter" id="address-counter"></div>
              </div>
              <div class="form-group">
                <label>Mã số thuế</label>
                <input type="text" id="tax-code" maxlength="20">
                <div class="char-counter" id="tax-code-counter"></div>
              </div>
            </div>
          </div>

          <!-- 4) CÀI ĐẶT -->
          <div class="form-card">
            <h2>4) Cài đặt</h2>
            <div class="form-row">
              <div class="form-group">
                <label>Đường dẫn tuỳ chỉnh</label>
                <input type="text" id="custom-path" maxlength="60" placeholder="vd: eventuser-2025">
                <div class="char-counter" id="path-counter"></div>
              </div>
              <div class="form-group">
                <label>Trạng thái</label>
                <?php $st=$_POST['status']??'active'; ?>
                <select name="status">
                  <option value="active" <?php echo $st==='active'?'selected':''; ?>>Kích hoạt</option>
                  <option value="draft"  <?php echo $st==='draft'?'selected':''; ?>>Nháp</option>
                  <option value="closed" <?php echo $st==='closed'?'selected':''; ?>>Đã đóng</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Thông điệp xác nhận</label>
              <input type="text" id="confirmation-message" maxlength="120" placeholder="Ví dụ: Cảm ơn bạn đã đăng ký!">
              <div class="char-counter" id="message-counter"></div>
            </div>
          </div>

          <div class="action-buttons">
            <button type="submit" class="btn btn-primary">Lưu sự kiện</button>
            <a href="javascript:history.back()" class="btn btn-secondary">Huỷ</a>
          </div>
        </form>
      </section>
    </main>
  </div>

  <!-- LIÊN KẾT JS GỘP -->
  <script src="/assets/js/add.js"></script>
</body>
</html>