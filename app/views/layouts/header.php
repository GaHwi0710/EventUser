<?php
/***************************************************
 * EvenUser - LAYOUT HEADER
 * - Partial include: app/views/layouts/header.php
 * - Dùng chung cho tất cả trang (home, list, detail, add, edit, v.v.)
 * - KHÔNG nhúng CSS/JS tại đây; mỗi trang tự link CSS/JS riêng.
 ***************************************************/
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// (Optional) Flash message helpers đơn giản
if (!function_exists('flash_set')) {
  function flash_set(string $type, string $msg): void {
    $_SESSION['_flash'] = ['type'=>$type,'msg'=>$msg];
  }
}
if (!function_exists('flash_get')) {
  function flash_get(): ?array {
    if (!empty($_SESSION['_flash'])) {
      $f = $_SESSION['_flash']; unset($_SESSION['_flash']);
      return $f;
    }
    return null;
  }
}

// Người dùng hiện tại
$currentName = $_SESSION['user_name'] ?? 'Account';
$isLoggedIn  = !empty($_SESSION['user_id']);
?>
<header class="header">
  <div class="container">
    <a href="/public/index.php" class="logo">
      <span class="logo-icon">EU</span> EvenUser
    </a>

    <!-- Ô tìm kiếm chung: đẩy về list.php -->
    <form class="search-container" action="/app/views/event/list.php" method="get">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" class="search-input" name="q" placeholder="Tìm sự kiện, địa điểm...">
        <button class="search-btn" type="submit">Tìm</button>
      </div>
    </form>

    <nav class="user-actions">
      <a class="btn-create" href="/app/views/event/add.php">
        <i class="fa-regular fa-calendar-plus"></i>Tạo sự kiện
      </a>
      <a class="btn-tickets" href="/app/views/event/list.php">
        <i class="fa-solid fa-ticket"></i>Danh sách
      </a>

      <?php if ($isLoggedIn): ?>
        <a class="btn-tickets" href="/app/views/user/my-events.php">
          <i class="fa-solid fa-list-check"></i> Sự kiện của tôi
        </a>
        <a class="user-profile" href="/app/views/user/profile.php" title="Trang cá nhân">
          <div class="avatar"><i class="fa-regular fa-user"></i></div>
          <span><?php echo htmlspecialchars($currentName); ?></span>
        </a>
        <a class="btn-tickets" href="/app/views/auth/logout.php">
          <i class="fa-solid fa-right-from-bracket"></i> Thoát
        </a>
      <?php else: ?>
        <a class="btn-tickets" href="/app/views/auth/login.php">
          <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
        </a>
        <a class="btn-tickets" href="/app/views/auth/register.php">
          <i class="fa-regular fa-id-badge"></i> Đăng ký
        </a>
      <?php endif; ?>
    </nav>
  </div>

  <?php if ($__f = flash_get()): ?>
    <div class="container" style="margin-top:10px">
      <div class="alert <?php echo $__f['type']==='success'?'alert-success':'alert-error'; ?>">
        <?php echo htmlspecialchars($__f['msg']); ?>
      </div>
    </div>
  <?php endif; ?>
</header>
