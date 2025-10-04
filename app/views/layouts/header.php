<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// =========================
// Helper flash message
// =========================
if (!function_exists('flash_set')) {
  function flash_set(string $type, string $msg): void {
    $_SESSION['_flash'] = ['type' => $type, 'msg' => $msg];
  }
}
if (!function_exists('flash_get')) {
  function flash_get(): ?array {
    if (!empty($_SESSION['_flash'])) {
      $f = $_SESSION['_flash'];
      unset($_SESSION['_flash']);
      return $f;
    }
    return null;
  }
}
$currentName = $_SESSION['user_name'] ?? 'Account';
$isLoggedIn  = !empty($_SESSION['user_id']);
require_once __DIR__ . '/../../config/config.php'; // BASE_URL & ASSETS_URL

// Tên file trang hiện tại (ví dụ: home.php -> home)
$page = basename($_SERVER['SCRIPT_NAME'], '.php');

// Xác định đường dẫn CSS tương ứng
$cssFile = ASSETS_URL . "css/{$page}.css";              // dùng cho href (trình duyệt)
$cssFullPath = __DIR__ . "/../../../assets/css/{$page}.css"; // dùng cho kiểm tra file thật
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EventUser - <?php echo ucfirst($page); ?></title>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- CSS trang tương ứng -->
  <?php if (file_exists($cssFullPath)): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssFile); ?>">
  <?php else: ?>
    <!-- fallback nếu chưa có file CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>../css/home.css">
  <?php endif; ?>
</head>
<body>

<header class="header">
  <div class="container">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>" class="logo">
      <span class="logo-icon">EU</span> EventUser
    </a>

    <!-- Ô tìm kiếm -->
    <form class="search-container" action="<?php echo BASE_URL; ?>app/views/event/list.php" method="get">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" class="search-input" name="q" placeholder="Tìm sự kiện, địa điểm...">
        <button class="search-btn" type="submit">Tìm</button>
      </div>
    </form>

    <!-- Thanh điều hướng -->
    <nav class="user-actions">
      <a class="btn-create" href="<?php echo BASE_URL; ?>app/views/event/add.php">
        <i class="fa-regular fa-calendar-plus"></i> Tạo sự kiện
      </a>
      <a class="btn-tickets" href="<?php echo BASE_URL; ?>app/views/event/list.php">
        <i class="fa-solid fa-ticket"></i> Danh sách
      </a>

      <?php if ($isLoggedIn): ?>
        <a class="btn-tickets" href="<?php echo BASE_URL; ?>app/views/user/my-events.php">
          <i class="fa-solid fa-list-check"></i> Sự kiện của tôi
        </a>
        <a class="user-profile" href="<?php echo BASE_URL; ?>app/views/user/profile.php">
          <div class="avatar"><i class="fa-regular fa-user"></i></div>
          <span><?php echo htmlspecialchars($currentName); ?></span>
        </a>
        <a class="btn-tickets" href="<?php echo BASE_URL; ?>app/views/auth/logout.php">
          <i class="fa-solid fa-right-from-bracket"></i> Thoát
        </a>
      <?php else: ?>
        <a class="btn-tickets" href="<?php echo BASE_URL; ?>app/views/auth/login.php">
          <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
        </a>
        <a class="btn-tickets" href="<?php echo BASE_URL; ?>app/views/auth/register.php">
          <i class="fa-regular fa-id-badge"></i> Đăng ký
        </a>
      <?php endif; ?>
    </nav>
  </div>

  <!-- Flash message -->
  <?php if ($__f = flash_get()): ?>
    <div class="container" style="margin-top:10px">
      <div class="alert <?php echo $__f['type'] === 'success' ? 'alert-success' : 'alert-error'; ?>">
        <?php echo htmlspecialchars($__f['msg']); ?>
      </div>
    </div>
  <?php endif; ?>
</header>
