<?php
/**
 * app/core/Database.php
 * PDO Singleton – kết nối 1 lần dùng mọi nơi
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        // Nếu bạn muốn, có thể tách sang file config riêng và require ở đây.
        $config = [
            'host'     => getenv('DB_HOST') ?: 'localhost',
            'dbname'   => getenv('DB_NAME') ?: 'eventuser',
            'username' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASS') ?: '',
            'charset'  => 'utf8mb4',
        ];

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (Throwable $e) {
            // Đừng echo lỗi nhạy cảm trên production
            throw new RuntimeException('Database connection failed: '.$e->getMessage(), 0, $e);
        }
    }

    /** Lấy singleton instance */
    public static function getInstance(): Database
    {
        if (!self::$instance) self::$instance = new self();
        return self::$instance;
    }

    /** Lấy PDO (nếu bạn muốn thao tác trực tiếp) */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /** Shortcut query có prepare */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st;
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
