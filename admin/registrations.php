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

// ✅ Duyệt đăng ký
if ($action === 'approve' && isset($_GET['id'])) {
    $registration->id = $_GET['id'];
    $registration->status = 'approved';
    $registration->updateStatus();
    setFlash('✅ Duyệt đăng ký thành công!', 'success');
    redirect(SITE_URL . '/admin/registrations.php');
}

// ✅ Từ chối đăng ký
if ($action === 'reject' && isset($_GET['id'])) {
    $registration->id = $_GET['id'];
    $registration->status = 'rejected';
    $registration->updateStatus();
    setFlash('🚫 Từ chối đăng ký thành công!', 'danger');
    redirect(SITE_URL . '/admin/registrations.php');
}

$registrations = $registration->readAll();

$pageTitle = "Quản lý đăng ký";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="dashboard-title mb-4 fw-bold">Quản lý đăng ký</h1>

    <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Sự kiện</th>
                        <th>Người dùng</th>
                        <th>Ngày đăng ký</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($registrations)): ?>
                        <?php foreach ($registrations as $r): ?>
                            <tr>
                                <td><?php echo $r['id']; ?></td>
                                <td><?php echo htmlspecialchars($r['event_title']); ?></td>
                                <td><?php echo htmlspecialchars($r['user_email']); ?></td>
                                <td><?php echo formatDate($r['registration_date']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $r['status'] == 'approved' ? 'success' : 
                                            ($r['status'] == 'rejected' ? 'danger' : 'warning');
                                    ?>">
                                        <?php 
                                        if ($r['status'] == 'approved') echo 'Đã duyệt';
                                        elseif ($r['status'] == 'rejected') echo 'Đã từ chối';
                                        else echo 'Chờ duyệt';
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($r['status'] == 'pending'): ?>
                                        <a href="?action=approve&id=<?php echo $r['id']; ?>" class="btn btn-success btn-sm" title="Duyệt">
                                            <i class="bi bi-check"></i>
                                        </a>
                                        <a href="?action=reject&id=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm" title="Từ chối">
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
                            <td colspan="6" class="text-muted py-4">Chưa có đăng ký nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

    .container {
        padding-top: 10px; /* tạo khoảng đệm nhẹ cho toàn bộ nội dung */
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
