<?php
/**
 * tests/UnitTest.php
 * Smoke test (không cần PHPUnit) cho EventUser
 * Chạy:  http://localhost/event-user/tests/UnitTest.php
 * Hoặc CLI: php tests/UnitTest.php
 */

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$ROOT = dirname(__DIR__);
$APP  = $ROOT . '/app';

// Core & autoload
require_once $APP . '/core/Database.php';
require_once $APP . '/core/Model.php';

// autoload models
spl_autoload_register(function ($class) use ($ROOT) {
  $m = "$ROOT/app/models/$class.php";
  $c = "$ROOT/app/controllers/$class.php";
  if (is_file($m)) require_once $m;
  elseif (is_file($c)) require_once $c;
});

$results = [];
$cleanup = [];

// helpers
function ok($cond, $msg){ global $results; $results[] = ['ok'=>$cond, 'msg'=>$msg]; return $cond; }
function eq($a,$b,$msg){ return ok($a === $b, $msg." (expected: ".var_export($b,true).", got: ".var_export($a,true).")"); }
function like($cond, $msg){ return ok((bool)$cond, $msg); }
function label($s){ echo ($s.PHP_EOL); }

// run in safe try/catch
try {
  // 1) Database connect
  $dbi = Database::getInstance();
  $pdo = $dbi->getConnection();
  ok($pdo instanceof PDO, "Database::getInstance() trả về PDO");

  // 2) Bảng tồn tại?
  $tables = ['users','events','event_tickets','registrations'];
  foreach ($tables as $t) {
    $st = $pdo->query("SHOW TABLES LIKE '$t'");
    ok($st->fetchColumn() !== false, "Bảng `$t` tồn tại");
  }

  // 3) USER: tạo user thử
  $userModel = class_exists('User') ? new User() : null;
  ok($userModel!==null, "Model User tồn tại");

  $suffix = substr(bin2hex(random_bytes(4)),0,6);
  $email  = "unit_$suffix@example.com";
  $pass   = "123456";
  $uid = $userModel->create([
    'email' => $email,
    'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
    'name' => "Unit Test $suffix",
    'role' => 'user'
  ]);
  like($uid>0, "Tạo user mới thành công (#$uid)");
  $cleanup['users'][] = $uid;

  $u = $userModel->findById($uid);
  ok($u && $u['email']===$email, "Lấy user theo ID đúng");
  ok($userModel->verifyPassword($u,$pass), "verifyPassword() đúng");

  // 4) EVENT: tạo sự kiện
  $eventModel = class_exists('Event') ? new Event() : null;
  ok($eventModel!==null, "Model Event tồn tại");

  $eid = $eventModel->create([
    'title'       => "Unit Event $suffix",
    'description' => "Mô tả test $suffix",
    'location'    => "Hà Nội",
    'image_url'   => '',
    'start_time'  => date('Y-m-d H:i:s', strtotime('+2 days')),
    'end_time'    => date('Y-m-d H:i:s', strtotime('+2 days +2 hours')),
    'status'      => 'active',
    'created_by'  => $uid
  ]);
  like($eid>0, "Tạo event mới thành công (#$eid)");
  $cleanup['events'][] = $eid;

  $e = $eventModel->findById($eid);
  ok($e && $e['title']==="Unit Event $suffix", "findById() event đúng");

  // 5) TICKET: thêm 2 loại vé & lấy giá min
  $ticketModel = class_exists('Ticket') ? new Ticket() : null;
  if ($ticketModel) {
    $tid1 = $ticketModel->create(['event_id'=>$eid,'name'=>'Standard','price'=>50000,'quantity'=>100]);
    $tid2 = $ticketModel->create(['event_id'=>$eid,'name'=>'VIP','price'=>150000,'quantity'=>20]);
    like($tid1>0 && $tid2>0, "Tạo ticket Standard/VIP thành công");
    $cleanup['tickets'] = [$tid1,$tid2];

    $min = $ticketModel->minPriceByEvent($eid);
    eq((float)$min, 50000.0, "minPriceByEvent() trả 50,000");
    $list = $ticketModel->listByEvent($eid);
    like(count($list)>=2, "listByEvent() trả >= 2 vé");
  } else {
    ok(true, "Bỏ qua test Ticket vì chưa có model Ticket");
  }

  // 6) REGISTRATION: đăng ký 1 vé
  $regModel = class_exists('Registration') ? new Registration() : null;
  if ($regModel) {
    $rid = $regModel->create([
      'user_id'  => $uid,
      'event_id' => $eid,
      'ticket_id'=> $ticketModel ? $tid1 : null,
      'quantity' => 2
    ]);
    like($rid>0, "Tạo registration thành công (#$rid)");
    $cleanup['registrations'][] = $rid;

    $myTickets = $regModel->listByUser($uid, 50);
    like(count($myTickets)>=1, "listByUser() trả về >= 1 kết quả");
  } else {
    ok(true, "Bỏ qua test Registration vì chưa có model Registration");
  }

  // 7) MY EVENTS: listByCreator()
  $page=1; $per=8;
  $res = $eventModel->listByCreator($uid, ['q'=>'Unit Event','status'=>''], $page, $per);
  like(isset($res['data']) && count($res['data'])>=1, "listByCreator() tìm thấy event của user");

  // 8) paginate() & listLatest()
  $pg = $eventModel->paginate(['q'=>'Unit Event'], 1, 10);
  like(isset($pg['data']) && count($pg['data'])>=1, "paginate() hoạt động");
  $latest = $eventModel->listLatest(3);
  like(is_array($latest), "listLatest() trả về mảng");

} catch (Throwable $e) {
  $results[] = ['ok'=>false, 'msg'=>"EXCEPTION: ".$e->getMessage()];
}

// Cleanup (tuỳ chọn: comment nếu muốn giữ lại dữ liệu kiểm thử)
try {
  $pdo = Database::getInstance()->getConnection();
  if (!empty($cleanup['registrations'])) {
    $ids = implode(',', array_map('intval',$cleanup['registrations']));
    $pdo->exec("DELETE FROM registrations WHERE id IN ($ids)");
  }
  if (!empty($cleanup['tickets'])) {
    $ids = implode(',', array_map('intval',$cleanup['tickets']));
    $pdo->exec("DELETE FROM event_tickets WHERE id IN ($ids)");
  }
  if (!empty($cleanup['events'])) {
    $ids = implode(',', array_map('intval',$cleanup['events']));
    $pdo->exec("DELETE FROM events WHERE id IN ($ids)");
  }
  if (!empty($cleanup['users'])) {
    $ids = implode(',', array_map('intval',$cleanup['users']));
    $pdo->exec("DELETE FROM users WHERE id IN ($ids)");
  }
} catch (Throwable $e) {
  $results[] = ['ok'=>false, 'msg'=>"CLEANUP ERROR: ".$e->getMessage()];
}

// Output
$isCli = (php_sapi_name() === 'cli');
$pass = array_sum(array_map(fn($r)=>$r['ok']?1:0, $results));
$total= count($results);

if ($isCli) {
  echo "EventUser UnitTest: $pass / $total passed".PHP_EOL;
  foreach ($results as $i=>$r) {
    echo sprintf("[%s] %s", $r['ok']?'OK ':'NG ', $r['msg']).PHP_EOL;
  }
  exit($pass===$total ? 0 : 1);
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>EventUser – Unit Test</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:#0b0f14;color:#e6f1ff;margin:0;padding:24px}
    h1{font-size:22px;margin:0 0 16px}
    .sum{margin:6px 0 16px;opacity:.8}
    table{width:100%;border-collapse:collapse;background:#0f1720;border:1px solid #243447}
    th,td{padding:10px;border-bottom:1px solid #1d2a39;text-align:left}
    th{background:#111827}
    .ok{color:#22c55e;font-weight:600}
    .ng{color:#ef4444;font-weight:600}
    .footer{margin-top:14px;font-size:12px;color:#92a3b0}
  </style>
</head>
<body>
  <h1>EventUser – Unit Test</h1>
  <div class="sum">Kết quả: <strong><?= $pass ?></strong> / <strong><?= $total ?></strong> testcase passed</div>
  <table>
    <thead><tr><th>#</th><th>Trạng thái</th><th>Mô tả</th></tr></thead>
    <tbody>
      <?php foreach ($results as $i=>$r): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td class="<?= $r['ok']?'ok':'ng' ?>"><?= $r['ok']?'OK':'FAIL' ?></td>
          <td><?= htmlspecialchars($r['msg']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="footer">Chạy lại trang này mỗi khi chỉnh backend để smoke test nhanh.</div>
</body>
</html>
