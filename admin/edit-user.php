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

$id = $_GET['id'] ?? 0;
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Không tìm thấy người dùng!");
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'] ?? 'user';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    $avatar = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $target_dir = "../uploads/avatars/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = uniqid() . "_" . basename($_FILES["avatar"]["name"]);
        $target_file = $target_dir . $filename;
        move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file);
        $avatar = $filename;
    }

    $stmt = $db->prepare("UPDATE users 
                          SET full_name = :full_name, email = :email, role = :role, 
                              phone = :phone, address = :address, avatar = :avatar, updated_at = NOW()
                          WHERE id = :id");
    $stmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':role' => $role,
        ':phone' => $phone,
        ':address' => $address,
        ':avatar' => $avatar,
        ':id' => $id
    ]);

    $message = "Cập nhật người dùng thành công!";
    $message_type = "success";

    $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = "Chỉnh sửa người dùng";
require_once '../includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4">✏️ Chỉnh sửa thông tin người dùng</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="role" class="form-select">
                <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Người dùng</option>
                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Ảnh đại diện</label><br>
            <?php if (!empty($user['avatar'])): ?>
                <img src="../uploads/avatars/<?php echo $user['avatar']; ?>" alt="Avatar" width="100" class="mb-2 rounded-circle"><br>
            <?php endif; ?>
            <input type="file" name="avatar" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="users.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
