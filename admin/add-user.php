<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    redirect(SITE_URL . '/index.php');
}

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'] ?? 'user';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    $avatar = null;
    if (!empty($_FILES['avatar']['name'])) {
        $target_dir = "../uploads/avatars/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = uniqid() . "_" . basename($_FILES["avatar"]["name"]);
        $target_file = $target_dir . $filename;
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file);
        $avatar = $filename;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO users (full_name, email, password, role, phone, address, avatar, created_at) 
                          VALUES (:full_name, :email, :password, :role, :phone, :address, :avatar, NOW())");
    $stmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':password' => $hashed,
        ':role' => $role,
        ':phone' => $phone,
        ':address' => $address,
        ':avatar' => $avatar
    ]);

    $message = "Thêm người dùng mới thành công!";
    $message_type = "success";
}

$pageTitle = "Thêm Người Dùng";
require_once '../includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4">➕ Thêm người dùng mới</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="address" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Ảnh đại diện</label>
            <input type="file" name="avatar" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="role" class="form-select">
                <option value="user">Người dùng</option>
                <option value="admin">Quản trị viên</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Thêm người dùng</button>
        <a href="users.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
