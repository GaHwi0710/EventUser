<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect(SITE_URL . '/index.php');
}

require_once '../classes/Database.php';

$database = new Database();
$db = $database->getConnection();

$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();

$total_events = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();

$total_registrations = $db->query("SELECT COUNT(*) FROM registrations")->fetchColumn();

$approved = $db->query("SELECT COUNT(*) FROM registrations WHERE status='approved'")->fetchColumn();
$pending  = $db->query("SELECT COUNT(*) FROM registrations WHERE status='pending'")->fetchColumn();
$rejected = $db->query("SELECT COUNT(*) FROM registrations WHERE status='rejected'")->fetchColumn();

$top_events = $db->query("
    SELECT event_title, COUNT(*) as total
    FROM registrations
    GROUP BY event_title
    ORDER BY total DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Báo Cáo & Thống Kê";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="dashboard-title">📊 Báo Cáo & Thống Kê</h1>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Người Dùng</h5>
                    <h2 class="fw-bold text-primary"><?php echo $total_users; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Sự Kiện</h5>
                    <h2 class="fw-bold text-success"><?php echo $total_events; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Lượt Đăng Ký</h5>
                    <h2 class="fw-bold text-warning"><?php echo $total_registrations; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-muted">Đã Duyệt</h5>
                    <h2 class="fw-bold text-success"><?php echo $approved; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">📈 Tình Trạng Đăng Ký</h5>
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">🏆 Top 5 Sự Kiện Nhiều Người Tham Gia</h5>
                    <canvas id="eventChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx1 = document.getElementById('statusChart');
new Chart(ctx1, {
    type: 'doughnut',
    data: {
        labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
        datasets: [{
            data: [<?php echo $approved; ?>, <?php echo $pending; ?>, <?php echo $rejected; ?>],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545']
        }]
    }
});

const ctx2 = document.getElementById('eventChart');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(',', array_map(fn($e) => "'" . $e['event_title'] . "'", $top_events)); ?>],
        datasets: [{
            label: 'Số lượt đăng ký',
            data: [<?php echo implode(',', array_map(fn($e) => $e['total'], $top_events)); ?>],
            backgroundColor: '#007bff'
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<style>
    .dashboard-title {
        font-size: 2rem;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
        .container {
        padding-top: 10px; 
    }

.card {
    transition: 0.3s;
}
.card:hover {
    transform: translateY(-3px);
}
</style>

<?php require_once '../includes/footer.php'; ?>
