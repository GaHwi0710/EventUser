<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect(SITE_URL . '/index.php');
}

require_once '../classes/Database.php';
require_once '../classes/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$action = $_GET['action'] ?? '';

// ✅ Xóa người dùng
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            setFlash('✅ Xóa người dùng thành công!', 'success');
        } else {
            setFlash('❌ Xóa người dùng thất bại!', 'danger');
        }
    } catch (Exception $e) {
        setFlash('Lỗi: ' . $e->getMessage(), 'danger');
    }

    redirect(SITE_URL . '/admin/users.php');
}

$users = $user->readAll();

$pageTitle = "Quản lý người dùng";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="dashboard-title fw-bold">Quản lý người dùng</h1>
        <a href="<?php echo SITE_URL; ?>/admin/add-user.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Thêm người dùng mới
        </a>
    </div>

    <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Vai trò</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user_item): ?>
                                <tr>
                                    <td><?php echo $user_item['id']; ?></td>
                                    <td><?php echo !empty($user_item['username']) ? htmlspecialchars($user_item['username']) : 'Không có'; ?></td>
                                    <td><?php echo htmlspecialchars($user_item['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user_item['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user_item['phone'] ?? 'Không có'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user_item['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                            <?php echo $user_item['role'] === 'admin' ? 'Quản trị viên' : 'Người dùng'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/edit-user.php?id=<?php echo $user_item['id']; ?>" class="btn btn-sm btn-primary" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/users.php?action=delete&id=<?php echo $user_item['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bạn có chắc muốn xóa người dùng này?');" 
                                           title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-muted py-4">Không có người dùng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
    .dashboard-title {
    font-size: 2rem;
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}
.card:hover {
    transform: translateY(-4px);
    transition: 0.3s;
}
.card img {
    border-radius: .5rem .5rem 0 0;
}
</style>

<?php require_once '../includes/footer.php'; ?>
