<?php
class Registration {
    private $conn;
    private $table_name = "registrations";
    
    public $id;
    public $event_title;
    public $user_email;
    public $registration_date;
    public $status;
    public $ticket_number;
    public $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    /** 🟢 Thêm đăng ký mới */
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                  (event_title, user_email, registration_date, status, ticket_number, notes)
                  VALUES (:event_title, :user_email, :registration_date, :status, :ticket_number, :notes)";
        
        $stmt = $this->conn->prepare($query);

        $this->event_title = htmlspecialchars(strip_tags($this->event_title));
        $this->user_email = htmlspecialchars(strip_tags($this->user_email));
        $this->registration_date = htmlspecialchars(strip_tags($this->registration_date));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->ticket_number = htmlspecialchars(strip_tags($this->ticket_number ?? ''));
        $this->notes = htmlspecialchars(strip_tags($this->notes ?? ''));

        $stmt->bindParam(":event_title", $this->event_title);
        $stmt->bindParam(":user_email", $this->user_email);
        $stmt->bindParam(":registration_date", $this->registration_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":ticket_number", $this->ticket_number);
        $stmt->bindParam(":notes", $this->notes);

        return $stmt->execute();
    }

    /** 🟡 Lấy tất cả đăng ký (admin dùng) */
    public function readAll() {
        $query = "SELECT * FROM {$this->table_name} ORDER BY registration_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** 🔵 Lấy danh sách người tham gia theo sự kiện */
    public function readByEvent() {
        $query = "SELECT * FROM {$this->table_name}
                  WHERE event_title = :event_title
                  ORDER BY registration_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":event_title", $this->event_title);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** 🟢 Lấy danh sách sự kiện theo người dùng (JOIN để hiển thị ảnh + id) */
    public function readByUser() {
        $query = "SELECT r.*, 
                         e.id AS event_id, 
                         e.title AS event_title, 
                         e.image AS event_image,
                         e.date AS event_date,
                         e.location AS event_location
                  FROM {$this->table_name} r
                  LEFT JOIN events e ON r.event_title = e.title
                  WHERE r.user_email = :user_email
                  ORDER BY r.registration_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $this->user_email);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** 🟣 Kiểm tra người dùng đã đăng ký sự kiện chưa */
    public function checkRegistration() {
        $query = "SELECT id FROM {$this->table_name}
                  WHERE event_title = :event_title AND user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":event_title", $this->event_title);
        $stmt->bindParam(":user_email", $this->user_email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /** 🟠 Cập nhật trạng thái (admin duyệt / từ chối) */
    public function updateStatus() {
        $query = "UPDATE {$this->table_name} 
                  SET status = :status 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    /** 🔴 Xóa đăng ký */
    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    /** 🧩 Lấy danh sách đăng ký cho dashboard (theo email user) */
    public function readByUserDashboard() {
        $query = "SELECT * FROM {$this->table_name} WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_email', $this->user_email);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
