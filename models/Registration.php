<?php
namespace models;

use core\Model;

/**
 * Class Registration
 * Model quản lý đăng ký tham gia sự kiện
 */
class Registration extends Model {
    protected $table = 'registrations';
    
    /**
     * Lấy tất cả đăng ký của một sự kiện
     * @param int $eventId
     * @return array
     */
    public function getByEvent($eventId) {
        $sql = "SELECT r.*, u.username, u.full_name, u.email 
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                WHERE r.event_id = ? AND r.status = 'confirmed'
                ORDER BY r.registered_at DESC";
        return $this->query($sql, [$eventId]);
    }
    
    /**
     * Lấy tất cả đăng ký của một người dùng
     * @param int $userId
     * @return array
     */
    public function getByUser($userId) {
        $sql = "SELECT r.*, e.title, e.event_date, e.event_time, e.location 
                FROM {$this->table} r
                JOIN events e ON r.event_id = e.id
                WHERE r.user_id = ? AND r.status = 'confirmed'
                ORDER BY e.event_date ASC";
        return $this->query($sql, [$userId]);
    }
    
    /**
     * Kiểm tra người dùng đã đăng ký sự kiện chưa
     * @param int $eventId
     * @param int $userId
     * @return bool
     */
    public function isRegistered($eventId, $userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE event_id = ? AND user_id = ? AND status = 'confirmed'";
        $result = $this->queryOne($sql, [$eventId, $userId]);
        return $result['count'] > 0;
    }
    
    /**
     * Đăng ký tham gia sự kiện
     * @param int $eventId
     * @param int $userId
     * @return int|false
     */
    public function register($eventId, $userId) {
        // Kiểm tra đã đăng ký chưa
        if ($this->isRegistered($eventId, $userId)) {
            return false;
        }
        
        // Kiểm tra sự kiện còn chỗ không
        $eventModel = new Event();
        $event = $eventModel->find($eventId);
        
        if ($event && $event['max_participants'] > 0) {
            $currentRegistrations = $this->countRegistrations($eventId);
            if ($currentRegistrations >= $event['max_participants']) {
                return false;
            }
        }
        
        // Thêm đăng ký
        $data = [
            'event_id' => $eventId,
            'user_id' => $userId,
            'status' => 'confirmed'
        ];
        
        return $this->create($data);
    }
    
    /**
     * Hủy đăng ký tham gia sự kiện
     * @param int $eventId
     * @param int $userId
     * @return bool
     */
    public function unregister($eventId, $userId) {
        $sql = "UPDATE {$this->table} 
                SET status = 'cancelled' 
                WHERE event_id = ? AND user_id = ?";
        return $this->execute($sql, [$eventId, $userId]);
    }
    
    /**
     * Đếm số người đã đăng ký một sự kiện
     * @param int $eventId
     * @return int
     */
    public function countRegistrations($eventId) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE event_id = ? AND status = 'confirmed'";
        $result = $this->queryOne($sql, [$eventId]);
        return $result['count'];
    }
    
    /**
     * Lấy thống kê đăng ký theo sự kiện
     * @return array
     */
    public function getStatsByEvent() {
        $sql = "SELECT e.id, e.title, e.event_date, COUNT(r.id) as registration_count
                FROM events e
                LEFT JOIN {$this->table} r ON e.id = r.event_id AND r.status = 'confirmed'
                GROUP BY e.id
                ORDER BY e.event_date ASC";
        return $this->query($sql);
    }
    
    /**
     * Lấy thống kê đăng ký theo người dùng
     * @return array
     */
    public function getStatsByUser() {
        $sql = "SELECT u.id, u.username, u.full_name, COUNT(r.id) as registration_count
                FROM users u
                LEFT JOIN {$this->table} r ON u.id = r.user_id AND r.status = 'confirmed'
                GROUP BY u.id
                ORDER BY registration_count DESC";
        return $this->query($sql);
    }
}
?>