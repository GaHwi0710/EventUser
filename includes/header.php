<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php
$isLoggedIn = isset($_SESSION['user_id']);
?>
<header class="header">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>/index.php">
                <img src="<?php echo SITE_URL; ?>/assets/images/logoevent.png" alt="EventUser" class="logo me-2">
                <span><?php echo SITE_NAME; ?></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($pageTitle) && $pageTitle == 'Trang Chủ') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($pageTitle) && $pageTitle == 'Sự Kiện') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/events/index.php">Sự kiện</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/features.php">Tính năng</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/pricing.php">Bảng giá</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/about.php">Về chúng tôi</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/contact.php">Liên hệ</a></li>
                </ul>

                <div class="d-flex">
                    <?php if ($isLoggedIn): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo $_SESSION['user_name'] ?? 'Tài khoản'; ?>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow">

                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <div class="dropdown-header text-center">
                                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Quản trị viên'); ?></h6>
                                        <small class="text-muted d-block mb-1"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@eventuser.vn'); ?></small>
                                        <span class="badge bg-danger">Admin</span>
                                        <hr class="dropdown-divider">
                                    </div>

                                    <li><h6 class="dropdown-header">Quản trị viên</h6></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/profile.php">
                                        <i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                                        <i class="bi bi-speedometer2 me-2"></i>Bảng điều khiển</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/add-event.php">
                                        <i class="bi bi-plus-circle me-2"></i>Thêm sự kiện</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/events.php">
                                        <i class="bi bi-calendar-event me-2"></i>Quản lý sự kiện</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/users.php">
                                        <i class="bi bi-people me-2"></i>Quản lý người dùng</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/registrations.php">
                                        <i class="bi bi-list-check me-2"></i>Đăng ký sự kiện</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>

                                <?php if ($_SESSION['user_role'] === 'user'): ?>
                                    <div class="dropdown-header text-center">
                                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Người dùng'); ?></h6>
                                        <small class="text-muted d-block mb-1"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'user@example.com'); ?></small>
                                        <span class="badge bg-primary">Thành viên</span>
                                        <hr class="dropdown-divider">
                                    </div>

                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/dashboard.php">
                                        <i class="bi bi-speedometer2 me-2"></i>Bảng điều khiển</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/profile.php">
                                        <i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/my-events.php">
                                        <i class="bi bi-calendar-heart me-2"></i>Sự kiện của tôi</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/auth/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-outline-light me-2">Đăng nhập</a>
                        <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary-custom">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
</header>
