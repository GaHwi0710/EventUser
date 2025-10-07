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

 $action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            setFlash('Xóa người dùng thành công', 'success');
        } else {
            setFlash('Xóa người dùng thất bại', 'danger');
        }
    } catch (Exception $e) {
        setFlash('Lỗi khi xóa người dùng: ' . $e->getMessage(), 'danger');
    }
    
    redirect(SITE_URL . '/admin/users.php');
}

 $users = $user->readAll();

 $pageTitle = "Quản Lý Người Dùng";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="dashboard-title">Quản Lý Người Dùng</h1>
        <a href="<?php echo SITE_URL; ?>/admin/add-user.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Thêm Người Dùng Mới
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
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
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user_item): ?>
                                <tr>
                                    <td><?php echo $user_item['id']; ?></td>
                                    <td><?php echo $row['username'] ?? $row['name'] ?? $row['full_name'] ?? 'Không có'; ?></td>
                                    <td><?php echo $user_item['full_name']; ?></td>
                                    <td><?php echo $user_item['email']; ?></td>
                                    <td><?php echo $user_item['phone']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user_item['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                                            <?php echo $user_item['role'] == 'admin' ? 'Quản trị viên' : 'Người dùng'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/edit-user.php?id=<?php echo $user_item['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin/users.php?action=delete&id=<?php echo $user_item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <h5 class="empty-state-title">Không có người dùng nào</h5>
                                        <p class="empty-state-text">Chưa có người dùng nào trong hệ thống.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>