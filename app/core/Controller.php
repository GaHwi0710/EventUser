<?php
/**
 * app/core/Controller.php
 * Base Controller – render view, redirect, json, csrf
 */
class Controller
{
    /** Render kèm header/footer (đúng với layouts đã làm) */
    protected function render(string $viewRelativePath, array $vars = []): void
    {
        $base = dirname(__DIR__) . '/views';
        $header = $base . '/layouts/header.php';
        $footer = $base . '/layouts/footer.php';
        $view   = $base . '/' . ltrim($viewRelativePath, '/');

        if (!is_file($view)) {
            http_response_code(404);
            echo "View not found: " . htmlspecialchars($viewRelativePath);
            return;
        }

        extract($vars, EXTR_SKIP);
        include $header;
        include $view;
        include $footer;
    }

    /** Chuyển hướng */
    protected function redirect(string $to): void
    {
        header("Location: $to");
        exit;
    }

    /** Trả JSON */
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** CSRF: tạo token */
    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['csrf'];
    }

    /** CSRF: kiểm tra token */
    protected function csrfVerify(?string $token): bool
    {
        return isset($_SESSION['csrf'])
            && is_string($token)
            && hash_equals($_SESSION['csrf'], $token);
    }
}
