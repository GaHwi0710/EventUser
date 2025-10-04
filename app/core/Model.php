<?php
/**
 * app/core/Model.php
 * Base Model – cung cấp $pdo và helper CRUD cơ bản
 */
require_once __DIR__ . '/Database.php';

abstract class Model
{
    /** @var PDO */
    protected PDO $pdo;

    public function __construct()
    {
        $dbi = Database::getInstance();
        $this->pdo = method_exists($dbi, 'getConnection') ? $dbi->getConnection() : $dbi;
    }

    protected function run(string $sql, array $params = []): PDOStatement
    {
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st;
    }

    protected function fetch(string $sql, array $params = []): ?array
    {
        $st = $this->run($sql, $params);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $st = $this->run($sql, $params);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    protected function lastId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
