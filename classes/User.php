<?php
class User {
    private $conn;
    public $table_name = "users";

    public $id;
    public $email;
    public $username;
    public $password;
    public $full_name;
    public $phone;
    public $role;
    public $avatar;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function userExists() {
        $query = "SELECT id FROM {$this->table_name} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function register() {
        $this->full_name = !empty($this->full_name) ? $this->full_name : 'Người dùng mới';
        $this->username = !empty($this->username) ? $this->username : explode('@', $this->email)[0];

        $query = "INSERT INTO {$this->table_name} 
                 (username, email, password, full_name, phone, role, created_at)
                 VALUES (:username, :email, :password, :full_name, :phone, :role, NOW())";

        $stmt = $this->conn->prepare($query);
        $hashed = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $hashed);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":role", $this->role);

        return $stmt->execute();
    }

    public function login() {
        $query = "SELECT * FROM {$this->table_name} 
                  WHERE email = :input OR username = :input 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":input", $this->email); 
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($this->password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getUserById($id = null) {
        if ($id === null && isset($_SESSION['user_id'])) {
            $id = $_SESSION['user_id'];
        }

        $query = "SELECT id, full_name, username, email, phone, avatar, role 
                  FROM {$this->table_name} 
                  WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile() {
        $query = "UPDATE {$this->table_name}
                  SET full_name = :full_name, phone = :phone, avatar = :avatar
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":avatar", $this->avatar);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT id, full_name, email, role, phone, created_at 
                  FROM {$this->table_name} 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
