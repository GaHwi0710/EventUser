<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$user->id = $_SESSION['user_id'];

$user_data = $user->getUserById($user->id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->full_name = $_POST['full_name'];
    $user->phone = $_POST['phone'];

    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = "../uploads/avatars/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES['avatar']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowed)) {
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                $user->avatar = $fileName;
            } else {
                setFlash('Không thể tải ảnh lên. Vui lòng thử lại.', 'danger');
            }
        } else {
            setFlash('Chỉ chấp nhận file ảnh JPG, PNG, GIF.', 'danger');
        }
    } else {
        $user->avatar = $user_data['avatar'];
    }

    if ($user->updateProfile()) {
        $_SESSION['user_name'] = $user->full_name;
        setFlash('Cập nhật thành công!', 'success');
        redirect(SITE_URL . '/user/profile.php');
    } else {
        setFlash('Cập nhật thất bại.', 'danger');
    }
}

$pageTitle = "Hồ sơ cá nhân";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-4">
            <div class="card text-center">
                <div class="card-body">
                    <?php
                    $avatarPath = !empty($user_data['avatar'])
                        ? SITE_URL . '/uploads/avatars/' . htmlspecialchars($user_data['avatar'])
                        : 'https://cdn-icons-png.flaticon.com/512/847/847969.png';
                    ?>
                    <img src="<?php echo $avatarPath; ?>" class="rounded-circle mb-3" alt="Avatar" width="150" height="150">

                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="file" name="avatar" class="form-control form-control-sm">
                        </div>
                        <h5><?php echo htmlspecialchars($user_data['full_name']); ?></h5>
                        <p class="text-muted">@<?php echo htmlspecialchars($user_data['username']); ?></p>
                        <span class="badge bg-<?php echo $user_data['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                            <?php echo $user_data['role'] == 'admin' ? 'Quản trị viên' : 'Người dùng'; ?>
                        </span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5>Thông tin cá nhân</h5></div>
                <div class="card-body">
                    <?php $flash = getFlash(); if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_data['username']); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone']); ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
