<?php
// public/index.php — Front Controller cho EvenUser
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$ROOT = dirname(__DIR__);
$APP  = $ROOT . '/app';

// --- Core cần thiết
require_once $APP . '/core/Database.php';
require_once $APP . '/core/Controller.php';
require_once $APP . '/core/Auth.php';

// --- Autoload models / controllers / core (phòng còn file chưa require)
spl_autoload_register(function ($class) use ($ROOT) {
    $paths = [
        "$ROOT/app/models/$class.php",
        "$ROOT/app/controllers/$class.php",
        "$ROOT/app/core/$class.php",
    ];
    foreach ($paths as $p) if (is_file($p)) { require_once $p; return; }
});

/**
 * render view thuần (home/list/detail nếu bạn muốn đi qua view trực tiếp)
 * -> sẽ bọc header/footer từ layouts
 */
function render_view(string $relative_view_path, array $vars = []): void {
    $base = dirname(__DIR__) . '/app/views';
    $header = "$base/layouts/header.php";
    $footer = "$base/layouts/footer.php";
    $view   = "$base/" . ltrim($relative_view_path, '/');

    if (!is_file($view)) {
        http_response_code(404);
        echo "View not found: " . htmlspecialchars($relative_view_path);
        return;
    }
    extract($vars, EXTR_SKIP);
    include $header;
    include $view;
    include $footer;
}

/**
 * ROUTER đơn giản qua ?r=controller/action
 * ví dụ: /?r=event/detail&id=5
 */
$r = $_GET['r'] ?? 'home/index';
$r = trim($r, '/');
[$controller, $action] = array_pad(explode('/', $r, 2), 2, 'index');

try {
    switch ("$controller/$action") {

        /* -------------------- HOME -------------------- */
        case 'home/index':
            // nếu bạn có home.php trong app/views/home/home.php
            render_view('home/home.php');
            break;

        /* -------------------- EVENT ------------------- */
        case 'event/list':
            // Nếu list.php tự query DB trong view, dùng render_view;
            // nếu muốn đi qua controller, dùng (new EventController())->index();
            (new EventController())->index();
            break;

        case 'event/detail':
            (new EventController())->detail();
            break;

        case 'event/add':
            (new EventController())->add();
            break;

        case 'event/edit':
            (new EventController())->edit();
            break;

        case 'event/delete':
            (new EventController())->delete();
            break;

        /* --------------------- AUTH ------------------- */
        case 'auth/login':
            (new AuthController())->login();
            break;

        case 'auth/register':
            (new AuthController())->register();
            break;

        case 'auth/logout':
            (new AuthController())->logout();
            break;

        /* --------------------- USER ------------------- */
        case 'user/profile':
            (new UserController())->profile();
            break;

        case 'user/my-events':
            (new UserController())->myEvents();
            break;

        /* -------------------- 404 --------------------- */
        default:
            http_response_code(404);
            echo "404 Not Found";
    }

} catch (Throwable $e) {
    // Tránh lộ thông tin nhạy cảm trên production
    http_response_code(500);
    echo "<h3>Server Error</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
