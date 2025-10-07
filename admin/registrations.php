<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect(SITE_URL . '/index.php');
}

require_once '../classes/Database.php';
require_once '../classes/Registration.php';

$database = new Database();
$db = $database->getConnection();

$registration = new Registration($db);
$action = $_GET['action'] ?? '';

// ✅ Xử lý duyệt đăng ký
if ($action === 'approve' && isset($_GET['id'])) {
    $registration->id = $_GET['id'];
    $registration->status = 'approved';

    if ($registration->updateStatus()) {
        setFlash('✅ Duyệt đăng ký thành công', 'success');
    } else {
        setFlash('❌ Duyệt đăng ký thất bại', 'danger');
    }
    redirect(SITE_URL . '/admin/registrations.php');
}

// ✅ Xử lý từ chối đăng ký
if ($action === 'reject' && isset($_GET['id'])) {
    $registration->id = $_GET['id'];
    $registration->status = 'rejected';

    if ($registration->updateStatus()) {
        setFlash('🚫 Từ chối đăng ký thành công', 'success');
    } else {
        setFlash('❌ Từ chối đăng ký thất bại', 'danger');
    }
    redirect(SITE_URL . '/admin/registrations.php');
}

// ✅ Lấy tất cả đăng ký
$registrations = $registration->readAll();

$pageTitle = "Quản Lý Đăng Ký";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="dashboard-title">Quản Lý Đăng Ký</h1>

    <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Sự kiện</th>
                            <th>Người dùng</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($registrations)): ?>
                            <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td><?php echo $reg['id']; ?></td>
                                    
                                    <!-- ✅ Sử dụng event_title thay vì event_id -->
                                    <td>
                                        <span class="fw-semibold text-primary">
                                            <?php echo htmlspecialchars($reg['event_title']); ?>
                                        </span>
                                    </td>
                                    
                                    <!-- ✅ Sử dụng user_email thay vì user_name -->
                                    <td><?php echo htmlspecialchars($reg['user_email']); ?></td>
                                    
                                    <td><?php echo formatDate($reg['registration_date']); ?></td>
                                    
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $reg['status'] === 'approved' ? 'success' : 
                                                 ($reg['status'] === 'rejected' ? 'danger' : 'warning');
                                        ?>">
                                            <?php 
                                            if ($reg['status'] === 'approved') echo 'Đã duyệt';
                                            elseif ($reg['status'] === 'rejected') echo 'Đã từ chối';
                                            else echo 'Chờ duyệt';
                                            ?>
                                        </span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if ($reg['status'] === 'pending'): ?>
                                            <a href="<?php echo SITE_URL; ?>/admin/registrations.php?action=approve&id=<?php echo $reg['id']; ?>" 
                                               class="btn btn-sm btn-success me-1" title="Duyệt">
                                                <i class="bi bi-check"></i>
                                            </a>
                                            <a href="<?php echo SITE_URL; ?>/admin/registrations.php?action=reject&id=<?php echo $reg['id']; ?>" 
                                               class="btn btn-sm btn-danger" title="Từ chối">
                                                <i class="bi bi-x"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Đã xử lý</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="empty-state">
                                        <div class="empty-state-icon mb-2">
                                            <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                                        </div>
                                        <h5 class="empty-state-title">Không có đăng ký nào</h5>
                                        <p class="text-muted">Hiện tại chưa có người dùng nào đăng ký sự kiện.</p>
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
