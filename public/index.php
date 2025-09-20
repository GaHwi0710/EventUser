<?php
/**
 * Front Controller - Điểm vào của ứng dụng
 * File này xử lý tất cả các request đến hệ thống
 */

// Bắt đầu session
session_start();

// Hiển thị lỗi (chỉ cho môi trường development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Định nghĩa hằng số cho đường dẫn
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('DATABASE_PATH', ROOT_PATH . '/database');

// Tự động nạp các lớp (Autoloading)
spl_autoload_register(function ($class) {
    // Chuyển đổi namespace thành đường dẫn file
    $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    
    // Kiểm tra file tồn tại
    if (file_exists($file)) {
        require_once $file;
    }
});

// Nạp các file cần thiết
require_once CONFIG_PATH . '/database.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';

// Khởi tạo router
$router = new Router();

// Định nghĩa các route

// Route cho trang chủ
$router->add('GET', '/', function() {
    $controller = new \controllers\EventController();
    return $controller->index();
});

// Authentication routes
$router->add('GET', '/login', function() {
    $controller = new \controllers\AuthController();
    return $controller->login();
});

$router->add('POST', '/login', function() {
    $controller = new \controllers\AuthController();
    return $controller->authenticate();
});

$router->add('GET', '/register', function() {
    $controller = new \controllers\AuthController();
    return $controller->register();
});

$router->add('POST', '/register', function() {
    $controller = new \controllers\AuthController();
    return $controller->store();
});

$router->add('GET', '/logout', function() {
    $controller = new \controllers\AuthController();
    return $controller->logout();
});

// Event routes
$router->add('GET', '/events', function() {
    $controller = new \controllers\EventController();
    return $controller->index();
});

$router->add('GET', '/events/{id}', function($id) {
    $controller = new \controllers\EventController();
    return $controller->show($id);
});

$router->add('GET', '/events/create', function() {
    $controller = new \controllers\EventController();
    return $controller->create();
});

$router->add('POST', '/events', function() {
    $controller = new \controllers\EventController();
    return $controller->store();
});

$router->add('GET', '/events/{id}/edit', function($id) {
    $controller = new \controllers\EventController();
    return $controller->edit($id);
});

$router->add('POST', '/events/{id}', function($id) {
    $controller = new \controllers\EventController();
    return $controller->update($id);
});

$router->add('POST', '/events/{id}/delete', function($id) {
    $controller = new \controllers\EventController();
    return $controller->destroy($id);
});

$router->add('POST', '/events/{id}/register', function($id) {
    $controller = new \controllers\EventController();
    return $controller->register($id);
});

$router->add('POST', '/events/{id}/unregister', function($id) {
    $controller = new \controllers\EventController();
    return $controller->unregister($id);
});

// Admin routes
$router->add('GET', '/admin', function() {
    $controller = new \controllers\AdminController();
    return $controller->dashboard();
});

$router->add('GET', '/admin/events', function() {
    $controller = new \controllers\AdminController();
    return $controller->events();
});

$router->add('GET', '/admin/users', function() {
    $controller = new \controllers\AdminController();
    return $controller->users();
});

// User routes
$router->add('GET', '/profile', function() {
    $controller = new \controllers\UserController();
    return $controller->profile();
});

$router->add('POST', '/profile', function() {
    $controller = new \controllers\UserController();
    return $controller->update();
});

// Xử lý request
$router->dispatch();