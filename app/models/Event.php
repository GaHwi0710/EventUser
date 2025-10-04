<?php
// app/models/Event.php
require_once __DIR__ . '/../core/Model.php';

class Event extends Model
{
    /** Lấy 1 sự kiện theo ID */
    public function findById(int $id): ?array {
        return $this->fetch("SELECT * FROM events WHERE id = ? LIMIT 1", [$id]);
    }

    /** Tạo sự kiện mới, trả về ID */
    public function create(array $data): int {
        // $data yêu cầu: title, description?, location?, image_url?, start_time, end_time, status?, created_by
        $cols = ['title', 'start_time', 'end_time', 'created_by', 'created_at'];
        $vals = [
            (string)$data['title'],
            (string)$data['start_time'],
            (string)$data['end_time'],
            (int)$data['created_by'],
            date('Y-m-d H:i:s')
        ];

        if (!empty($data['description'])) { $cols[]='description'; $vals[]=$data['description']; }
        if (!empty($data['location']))    { $cols[]='location';    $vals[]=$data['location']; }
        if (!empty($data['image_url']))   { $cols[]='image_url';   $vals[]=$data['image_url']; }
        $cols[] = 'status'; $vals[] = $data['status'] ?? 'active';

        $colSql = '`' . implode('`,`', $cols) . '`';
        $qm = rtrim(str_repeat('?,', count($cols)), ',');
        $this->run("INSERT INTO events ($colSql) VALUES ($qm)", $vals);
        return (int)$this->lastId();
    }

    /** Cập nhật sự kiện */
    public function update(int $id, array $data): bool {
        $sets = []; $vals = [];

        foreach (['title','description','location','image_url','start_time','end_time','status'] as $f) {
            if (array_key_exists($f, $data)) { $sets[] = "$f = ?"; $vals[] = $data[$f]; }
        }
        if (!$sets) return true;
        $vals[] = $id;

        $this->run("UPDATE events SET ".implode(',', $sets)." WHERE id = ?", $vals);
        return true;
    }

    /** Xoá: mặc định soft-delete bằng status='closed' nếu có cột status */
    public function delete(int $id, bool $soft = true): bool {
        if ($soft) {
            $this->run("UPDATE events SET status = 'closed' WHERE id = ?", [$id]);
        } else {
            $this->run("DELETE FROM events WHERE id = ?", [$id]);
        }
        return true;
    }

    /** Danh sách mới nhất cho Home */
    public function listLatest(int $limit = 6): array {
        // Ưu tiên start_time desc, fallback id desc
        return $this->fetchAll(
            "SELECT id, title, location, image_url, start_time, end_time
             FROM events
             ORDER BY start_time DESC, id DESC
             LIMIT ?", [$limit]
        );
    }

    /**
     * Phân trang + lọc cho trang list:
     * $filters: ['q'?, 'from'?, 'to'?, 'status'?]
     * return: ['data'=>[], 'total'=>int]
     */
    public function paginate(array $filters, int $page = 1, int $perPage = 9): array {
        $where = []; $params = [];

        if (!empty($filters['q'])) {
            $where[] = "title LIKE ?"; $params[] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['from'])) {
            $where[] = "start_time >= ?"; $params[] = $filters['from'] . ' 00:00:00';
        }
        if (!empty($filters['to'])) {
            $where[] = "end_time <= ?"; $params[] = $filters['to'] . ' 23:59:59';
        }
        if (!empty($filters['status'])) {
            $where[] = "status = ?"; $params[] = $filters['status'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $count = (int)$this->fetch("SELECT COUNT(*) AS c FROM events $whereSql", $params)['c'];

        $offset = max(0, ($page - 1) * $perPage);
        $data = $this->fetchAll(
            "SELECT id, title, description, location, image_url, start_time, end_time, status
             FROM events
             $whereSql
             ORDER BY start_time DESC, id DESC
             LIMIT $perPage OFFSET $offset", $params
        );

        return ['data' => $data, 'total' => $count];
    }

    /**
     * Danh sách sự kiện theo người tạo (My Events)
     * $filters: ['q'?, 'status'?]
     */
    public function listByCreator(int $userId, array $filters, int $page = 1, int $perPage = 8): array {
        $where = ["created_by = ?"]; $params = [$userId];

        if (!empty($filters['q'])) {
            $where[] = "title LIKE ?"; $params[] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = "status = ?"; $params[] = $filters['status'];
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $count = (int)$this->fetch("SELECT COUNT(*) AS c FROM events $whereSql", $params)['c'];

        $offset = max(0, ($page - 1) * $perPage);
        $data = $this->fetchAll(
            "SELECT id, title, location, image_url, start_time, end_time, status
             FROM events
             $whereSql
             ORDER BY start_time DESC, id DESC
             LIMIT $perPage OFFSET $offset", $params
        );

        return ['data' => $data, 'total' => $count];
    }

    /**
     * Giá thấp nhất của sự kiện (nếu có bảng event_tickets)
     * Trả về null nếu không có vé nào.
     */
    public function minTicketPrice(int $eventId): ?float {
        $row = $this->fetch(
            "SELECT MIN(price) AS p FROM event_tickets WHERE event_id = ?",
            [$eventId]
        );
        if (!$row || $row['p'] === null) return null;
        return (float)$row['p'];
    }
}
