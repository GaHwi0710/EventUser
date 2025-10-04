<?php
// app/models/User.php
require_once __DIR__ . '/../core/Model.php';

class User extends Model
{
    public function findById(int $id): ?array {
        return $this->fetch("SELECT * FROM users WHERE id = ? LIMIT 1", [$id]);
    }

    public function findByEmailOrPhone(string $value): ?array {
        return $this->fetch(
            "SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1",
            [$value, $value]
        );
    }

    public function existsEmail(string $email): bool {
        $row = $this->fetch("SELECT id FROM users WHERE email = ? LIMIT 1", [$email]);
        return (bool)$row;
    }

    public function existsPhone(string $phone): bool {
        $row = $this->fetch("SELECT id FROM users WHERE phone = ? LIMIT 1", [$phone]);
        return (bool)$row;
    }

    /**
     * $data: [
     *   'email'?, 'phone'?, 'password_hash'(required),
     *   'name'?, 'role'?
     * ]
     */
    public function create(array $data): int {
        $cols = ['password_hash'];
        $vals = [$data['password_hash']];

        if (!empty($data['email'])) { $cols[] = 'email'; $vals[] = $data['email']; }
        if (!empty($data['phone'])) { $cols[] = 'phone'; $vals[] = $data['phone']; }
        if (!empty($data['name']))  { $cols[] = 'name';  $vals[] = $data['name'];  }

        $cols[] = 'role';       $vals[] = $data['role'] ?? 'user';
        $cols[] = 'created_at'; $vals[] = date('Y-m-d H:i:s');

        $colSql = '`' . implode('`,`', $cols) . '`';
        $qm = rtrim(str_repeat('?,', count($cols)), ',');
        $this->run("INSERT INTO users ($colSql) VALUES ($qm)", $vals);
        return (int)$this->lastId();
    }

    /** Cho phép cập nhật name/phone (nếu truyền vào). */
    public function updateProfile(int $id, array $data): bool {
        $sets = []; $vals = [];
        if (array_key_exists('name', $data))  { $sets[] = "name = ?";  $vals[] = $data['name']; }
        if (array_key_exists('phone', $data)) { $sets[] = "phone = ?"; $vals[] = $data['phone']; }
        if (!$sets) return true;
        $vals[] = $id;
        $this->run("UPDATE users SET ".implode(',', $sets)." WHERE id = ?", $vals);
        return true;
    }

    public function setPassword(int $id, string $passwordHash): bool {
        $this->run("UPDATE users SET password_hash = ? WHERE id = ?", [$passwordHash, $id]);
        return true;
    }

    public function verifyPassword(array $user, string $plain): bool {
        return !empty($user['password_hash']) && password_verify($plain, $user['password_hash']);
    }
}
