<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>/index.php">
    <img src="<?php echo SITE_URL; ?>/assets/images/logoevent.png" 
         alt="EventUser Logo" 
         style="height: 55px; width: auto; object-fit: contain; margin-right: 10px;">
    <span class="fw-bold fs-4 text-white"><?php echo SITE_NAME; ?></span>
</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/index.php">Trang Chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/about.php">Về Chúng Tôi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'events/') !== false ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/events/index.php">Sự Kiện</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'features.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/features.php">Tính Năng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pricing.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/pricing.php">Bảng Giá</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/contact.php">Liên Hệ</a>
                </li>
                <li><a href="<?php echo SITE_URL; ?>/admin/profile.php" class="dropdown-item">
                    <i class="bi bi-person-circle me-1"></i> Hồ sơ admin
                </a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/profile.php">Hồ Sơ</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/my-events.php">Sự Kiện Của Tôi</a></li>
                            <?php if (isAdmin()): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                                <i class="bi bi-shield-lock me-1"></i>Quản Trị
                            </a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/auth/logout.php">Đăng Xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/auth/login.php">Đăng Nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/auth/register.php">Đăng Ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div