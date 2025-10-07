<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlash('Bạn không có quyền truy cập trang này!', 'danger');
    redirect(SITE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$currentUser = $user->getUserById($_SESSION['user_id']);

$pageTitle = "Hồ sơ quản trị viên";
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->id = $_SESSION['user_id'];
    $user->full_name = trim($_POST['full_name']);
    $user->phone = trim($_POST['phone']);
    $user->avatar = $currentUser['avatar'];

    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = '../uploads/avatars/';
        $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
            $user->avatar = $fileName;
        }
    }

    if ($user->updateProfile()) {
        setFlash('Cập nhật hồ sơ thành công!', 'success');
        redirect(SITE_URL . '/admin/profile.php');
    } else {
        setFlash('Cập nhật thất bại, vui lòng thử lại.', 'danger');
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-person-circle me-2"></i>Hồ Sơ Quản Trị Viên</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($flash = getFlash()): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="text-center mb-4">
                            <?php
                            $avatarPath = !empty($currentUser['avatar'])
                                ? SITE_URL . '/uploads/avatars/' . htmlspecialchars($currentUser['avatar'])
                                : 'https://ssl.gstatic.com/accounts/ui/avatar_2x.png';
                            ?>
                            <img src="<?php echo $avatarPath; ?>" alt="Avatar"
                                 class="rounded-circle shadow" width="130" height="130">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên đầy đủ</label>
                            <input type="text" name="full_name" class="form-control"
                                   value="<?php echo htmlspecialchars($currentUser['full_name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control"
                                   value="<?php echo htmlspecialchars($currentUser['email']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?php echo htmlspecialchars($currentUser['phone']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ảnh đại diện mới</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i>Lưu thay đổi
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
