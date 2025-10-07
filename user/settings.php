<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php');
}

require_once '../classes/Database.php';
require_once '../classes/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$user->id = $_SESSION['user_id'];
$user_data = $user->getUserById();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $user->id = $_SESSION['user_id'];
    $avatarFileName = $user_data['avatar'] ?? '';

    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = '../uploads/avatars/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileInfo = pathinfo($_FILES['avatar']['name']);
        $extension = strtolower($fileInfo['extension']);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extension, $allowedExtensions)) {
            $avatarFileName = uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $avatarFileName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                if (!empty($user_data['avatar']) && file_exists($uploadDir . $user_data['avatar'])) {
                    unlink($uploadDir . $user_data['avatar']);
                }
            } else {
                setFlash('Không thể tải lên ảnh đại diện', 'danger');
                $avatarFileName = $user_data['avatar'] ?? '';
            }
        } else {
            setFlash('Chỉ chấp nhận JPG, JPEG, PNG, GIF', 'danger');
        }
    }

    $updateFields = [];
    $params = [':id' => $_SESSION['user_id']];

    if (isset($_POST['full_name'])) {
        $updateFields[] = 'full_name = :full_name';
        $params[':full_name'] = $_POST['full_name'];
    }

    if (isset($_POST['phone'])) {
        $updateFields[] = 'phone = :phone';
        $params[':phone'] = $_POST['phone'];
    }

    if (!empty($avatarFileName)) {
        $updateFields[] = 'avatar = :avatar';
        $params[':avatar'] = $avatarFileName;
    }

    if (!empty($updateFields)) {
        $query = "UPDATE " . $user->table_name . " SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $stmt = $db->prepare($query);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);

        if ($stmt->execute()) {
            $_SESSION['user_name'] = $_POST['full_name'] ?? $_SESSION['user_name'];
            $_SESSION['user_avatar'] = $avatarFileName;
            setFlash('Cập nhật thông tin thành công!', 'success');
            redirect(SITE_URL . '/user/settings.php');
        } else setFlash('Cập nhật thất bại, vui lòng thử lại', 'danger');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password != $confirm_password) {
        setFlash('Mật khẩu mới và xác nhận không khớp', 'danger');
    } elseif (strlen($new_password) < 6) {
        setFlash('Mật khẩu phải có ít nhất 6 ký tự', 'danger');
    } else {
        $user->id = $_SESSION['user_id'];
        $user_data = $user->getUserById();

        if (password_verify($current_password, $user_data['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE " . $user->table_name . " SET password = :password WHERE id = :id");
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":id", $_SESSION['user_id']);
            if ($stmt->execute()) {
                setFlash('Đổi mật khẩu thành công!', 'success');
                redirect(SITE_URL . '/user/settings.php');
            } else {
                setFlash('Đổi mật khẩu thất bại', 'danger');
            }
        } else {
            setFlash('Mật khẩu hiện tại không đúng', 'danger');
        }
    }
}

$pageTitle = "Cài Đặt";
require_once '../includes/header.php';
?>

<section class="settings-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="settings-sidebar">
                    <div class="user-profile">
                        <div class="profile-avatar">
                            <?php
                            $avatarPath = !empty($user_data['avatar']) ? SITE_URL . '/uploads/avatars/' . htmlspecialchars($user_data['avatar'])
                                : SITE_URL . '/assets/images/default-avatar.png';
                            ?>
                            <img src="<?php echo $avatarPath; ?>" alt="User Avatar">
                        </div>
                        <h4><?php echo htmlspecialchars($user_data['full_name'] ?? $user_data['name'] ?? 'Người dùng'); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($user_data['email'] ?? ''); ?></p>
                        <span class="badge bg-<?php echo (($user_data['role'] ?? '') == 'admin') ? 'danger' : 'primary'; ?>">
                            <?php echo (($user_data['role'] ?? '') == 'admin') ? 'Quản trị viên' : 'Người dùng'; ?>
                        </span>
                    </div>

                    <div class="sidebar-menu">
                        <div class="menu-item"><a href="<?php echo SITE_URL; ?>/user/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Bảng điều khiển</a></div>
                        <div class="menu-item"><a href="<?php echo SITE_URL; ?>/user/profile.php"><i class="bi bi-person me-2"></i> Hồ sơ cá nhân</a></div>
                        <div class="menu-item"><a href="<?php echo SITE_URL; ?>/user/my-events.php"><i class="bi bi-calendar-event me-2"></i> Sự kiện của tôi</a></div>
                        <div class="menu-item active"><a href="<?php echo SITE_URL; ?>/user/settings.php"><i class="bi bi-gear me-2"></i> Cài đặt</a></div>
                        <div class="menu-item"><a href="<?php echo SITE_URL; ?>/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="settings-content">
                    <h2>Cài Đặt Tài Khoản</h2>

                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile">Thông tin cá nhân</button></li>
                        <li class="nav-item"><button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password">Mật khẩu</button></li>
                        <li class="nav-item"><button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications">Thông báo</button></li>
                    </ul>

                    <div class="tab-content" id="settingsTabsContent">
                        <div class="tab-pane fade show active" id="profile">
                            <div class="settings-form">
                                <?php $flash = getFlash(); if ($flash): ?>
                                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                                        <?php echo $flash['message']; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="update_profile" value="1">
                                    <div class="mb-4 text-center">
                                        <div class="avatar-preview">
                                            <img src="<?php echo $avatarPath; ?>" id="avatarPreview" alt="User Avatar">
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" id="avatarUpload" name="avatar" accept="image/*">
                                            <label for="avatarUpload" class="btn btn-sm btn-outline-primary mt-2">Thay đổi ảnh đại diện</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="full_name" class="form-label">Họ và tên</label>
                                            <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name'] ?? $user_data['name'] ?? ''); ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3 mt-3">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="role" class="form-label">Vai trò</label>
                                        <input type="text" class="form-control" value="<?php echo (($user_data['role'] ?? '') == 'admin') ? 'Quản trị viên' : 'Người dùng'; ?>" disabled>
                                    </div>

                                    <div class="mb-3">
                                        <label for="created_at" class="form-label">Ngày tham gia</label>
                                        <input type="text" class="form-control" value="<?php echo formatDate($user_data['created_at'] ?? $user_data['created_time'] ?? date('Y-m-d')); ?>" disabled>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="password">
                            <form method="post">
                                <input type="hidden" name="change_password" value="1">
                                <div class="mb-3">
                                    <label>Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label>Mật khẩu mới</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label>Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="notifications">
                            <p>Bạn sẽ sớm có thể cài đặt tuỳ chọn thông báo tại đây.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('avatarUpload').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
