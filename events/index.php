<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/Event.php';

$database = new Database();
$db = $database->getConnection();

$event = new Event($db);
$events = $event->readAll();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$pageTitle = "Danh Sách Sự Kiện";
require_once '../includes/header.php';
?>

<section class="section py-5">
    <div class="container">
        <h1 class="section-title text-center mb-4 fw-bold">Danh Sách Sự Kiện</h1>

        <!-- Bộ lọc tìm kiếm -->
        <div class="search-filter mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <form method="get" action="<?php echo SITE_URL; ?>/events/index.php">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                   placeholder="Tìm kiếm sự kiện..."
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-md-6">
                    <form method="get" action="<?php echo SITE_URL; ?>/events/index.php">
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="">Tất cả danh mục</option>
                            <option value="Công nghệ" <?php echo $category == 'Công nghệ' ? 'selected' : ''; ?>>Công nghệ</option>
                            <option value="Kinh doanh" <?php echo $category == 'Kinh doanh' ? 'selected' : ''; ?>>Kinh doanh</option>
                            <option value="Marketing" <?php echo $category == 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                            <option value="Giáo dục" <?php echo $category == 'Giáo dục' ? 'selected' : ''; ?>>Giáo dục</option>
                            <option value="Thể thao" <?php echo $category == 'Thể thao' ? 'selected' : ''; ?>>Thể thao</option>
                            <option value="Khác" <?php echo $category == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                        </select>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách sự kiện -->
        <div class="row" id="eventsList">
            <?php if (!empty($events)): ?>
                <?php 
                $found = false;
                foreach ($events as $event_item):
                    $matchSearch = ($search == '' ||
                        stripos($event_item['title'], $search) !== false ||
                        stripos($event_item['description'], $search) !== false);

                    $matchCategory = ($category == '' ||
                        ($event_item['category_name'] ?? '') == $category);

                    if ($matchSearch && $matchCategory):
                        $found = true;

                        // 🧩 Xử lý ảnh an toàn tuyệt đối:
                        $imageFile = trim($event_item['image'] ?? '');
                        $localPath = realpath(__DIR__ . '/../uploads/events/' . $imageFile);
                        $hasImage = $imageFile && $localPath && file_exists($localPath);
                        $imagePath = $hasImage
                            ? SITE_URL . '/uploads/events/' . htmlspecialchars($imageFile)
                            : SITE_URL . '/assets/images/default-event.jpg';
                ?>
                <div class="col-md-4 mb-4">
                    <div class="event-card h-100 shadow-sm rounded-4 overflow-hidden border">
                        <img src="<?php echo $imagePath; ?>" 
                             alt="<?php echo htmlspecialchars($event_item['title']); ?>" 
                             class="event-img w-100" style="height:220px;object-fit:cover;">
                        <div class="event-body p-3">
                            <div class="event-date mb-2 text-muted small">
                                <i class="bi bi-calendar-event me-1"></i>
                                <?php echo formatDate($event_item['start_date'] ?? $event_item['date']); ?>
                                | <?php echo htmlspecialchars($event_item['time']); ?>
                            </div>
                            <h3 class="event-title h5 mb-2 fw-bold"><?php echo htmlspecialchars($event_item['title']); ?></h3>
                            <p class="event-description mb-3 text-secondary">
                                <?php echo limitText($event_item['description'], 100); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary px-3 py-2">
                                    <?php echo htmlspecialchars($event_item['category_name'] ?? 'Không có danh mục'); ?>
                                </span>
                                <a href="detail.php?id=<?php echo $event_item['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    Xem Chi Tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; endforeach; ?>

                <?php if (!$found): ?>
                    <div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <div class="empty-state-icon mb-3">
                                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            </div>
                            <h4 class="empty-state-title mb-2">Không có sự kiện phù hợp</h4>
                            <p class="text-muted">Hãy thử lại với từ khóa hoặc danh mục khác.</p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <div class="empty-state-icon mb-3">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                        </div>
                        <h4 class="empty-state-title mb-2">Không có sự kiện nào</h4>
                        <p class="text-muted">Hiện chưa có dữ liệu sự kiện.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
