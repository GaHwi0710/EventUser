<?php
// app/models/Registration.php
require_once __DIR__ . '/../core/Model.php';

/**
 * Bảng registrations:
 * id (PK), user_id, event_id, ticket_id (nullable nếu free), quantity, created_at
 */
class Registration extends Model
{
    /**
     * $data: ['user_id','event_id','ticket_id'?,'quantity']
     */
    public function create(array $data): int {
        $cols = ['user_id','event_id','quantity','created_at'];
        $vals = [(int)$data['user_id'], (int)$data['event_id'], (int)$data['quantity'], date('Y-m-d H:i:s')];

        if (!empty($data['ticket_id'])) {
            $cols[] = 'ticket_id';
            $vals[] = (int)$data['ticket_id'];
        }

        $colSql = '`' . implode('`,`', $cols) . '`';
        $qm = rtrim(str_repeat('?,', count($cols)), ',');
        $this->run("INSERT INTO registrations ($colSql) VALUES ($qm)", $vals);
        return (int)$this->lastId();
    }

    /** Vé của một user (dùng ở profile) – join events và event_tickets (nếu có). */
    public function listByUser(int $userId, int $limit = 50): array {
        $sql = "SELECT r.*,
                       e.title, e.location, e.start_time, e.image_url,
                       t.name  AS ticket_name,
                       t.price AS ticket_price
                FROM registrations r
                JOIN events e ON r.event_id = e.id
                LEFT JOIN event_tickets t ON r.ticket_id = t.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
                LIMIT ?";
        return $this->fetchAll($sql, [$userId, $limit]);
    }

    /** Danh sách đăng ký theo sự kiện (nếu chủ sự kiện cần xem). */
    public function listByEvent(int $eventId): array {
        $sql = "SELECT r.*, u.name AS user_name, u.email, u.phone,
                       t.name AS ticket_name, t.price AS ticket_price
                FROM registrations r
                JOIN users u ON r.user_id = u.id
                LEFT JOIN event_tickets t ON r.ticket_id = t.id
                WHERE r.event_id = ?
                ORDER BY r.created_at DESC";
        return $this->fetchAll($sql, [$eventId]);
    }
}
