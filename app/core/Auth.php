<?php
// app/core/Auth.php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/User.php';

class Auth
{
    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int {
        return self::check() ? (int)$_SESSION['user_id'] : null;
    }

    public static function user(): ?array {
        if (!self::check()) return null;
        static $cached = null;
        if ($cached !== null) return $cached;
        $u = (new User())->findById(self::id());
        return $cached = $u ?: null;
    }

    public static function login(array $user): void {
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_name'] = !empty($user['name']) ? (string)$user['name'] : 'User';
        $_SESSION['user_role'] = !empty($user['role']) ? (string)$user['role'] : 'user';
        if (function_exists('session_regenerate_id')) { @session_regenerate_id(true); }
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        @session_destroy();
    }

    public static function isAdmin(): bool {
        return self::check() && (($_SESSION['user_role'] ?? 'user') === 'admin');
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            header('Location: /public/index.php?r=auth/login');
            exit;
        }
    }
}
