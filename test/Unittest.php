<?php
/**
 * Unit Test cho Registration Model
 */

// Include các file cần thiết
require_once '../config/database.php';
require_once '../app/core/Database.php';
require_once '../app/core/Model.php';
require_once '../app/models/Event.php';
require_once '../app/models/Registration.php';

class RegistrationTest {
    private $db;
    private $registrationModel;
    private $eventModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Khởi tạo database
        $this->db = \core\Database::getInstance();
        $this->registrationModel = new \models\Registration();
        $this->eventModel = new \models\Event();
        
        echo "<h2>Unit Test - Registration Model</h2>";
        echo "<hr>";
    }
    
    /**
     * Test đăng ký sự kiện
     */
    public function testRegister() {
        echo "<h3>Test 1: Đăng ký sự kiện</h3>";
        
        // Tạo sự kiện test
        $eventId = $this->eventModel->create([
            'title' => 'Test Event',
            'description' => 'Test Event Description',
            'event_date' => date('Y-m-d', strtotime('+7 days')),
            'event_time' => '10:00:00',
            'location' => 'Test Location',
            'max_participants' => 10,
            'created_by' => 1
        ]);
        
        // Test đăng ký thành công
        $userId = 2; // user1
        $result = $this->registrationModel->register($eventId, $userId);
        
        if ($result) {
            echo "<p style='color: green;'>✓ Đăng ký thành công</p>";
            
            // Test đăng ký lại (phải thất bại)
            $result2 = $this->registrationModel->register($eventId, $userId);
            if (!$result2) {
                echo "<p style='color: green;'>✓ Ngăn chặn đăng ký trùng lặp thành công</p>";
            } else {
                echo "<p style='color: red;'>✗ Không ngăn chặn được đăng ký trùng lặp</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Đăng ký thất bại</p>";
        }
        
        // Cleanup
        $this->db->execute("DELETE FROM registrations WHERE event_id = ?", [$eventId]);
        $this->db->execute("DELETE FROM events WHERE id = ?", [$eventId]);
        
        echo "<hr>";
    }
    
    /**
     * Test hủy đăng ký
     */
    public function testUnregister() {
        echo "<h3>Test 2: Hủy đăng ký</h3>";
        
        // Tạo sự kiện test
        $eventId = $this->eventModel->create([
            'title' => 'Test Event 2',
            'description' => 'Test Event Description 2',
            'event_date' => date('Y-m-d', strtotime('+7 days')),
            'event_time' => '10:00:00',
            'location' => 'Test Location 2',
            'max_participants' => 10,
            'created_by' => 1
        ]);
        
        // Đăng ký trước
        $userId = 3; // user2
        $this->registrationModel->register($eventId, $userId);
        
        // Test hủy đăng ký
        $result = $this->registrationModel->unregister($eventId, $userId);
        
        if ($result) {
            echo "<p style='color: green;'>✓ Hủy đăng ký thành công</p>";
            
            // Kiểm tra xem đã hủy chưa
            $isRegistered = $this->registrationModel->isRegistered($eventId, $userId);
            if (!$isRegistered) {
                echo "<p style='color: green;'>✓ Xác nhận hủy đăng ký thành công</p>";
            } else {
                echo "<p style='color: red;'>✗ Không xác nhận được hủy đăng ký</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Hủy đăng ký thất bại</p>";
        }
        
        // Cleanup
        $this->db->execute("DELETE FROM registrations WHERE event_id = ?", [$eventId]);
        $this->db->execute("DELETE FROM events WHERE id = ?", [$eventId]);
        
        echo "<hr>";
    }
    
    /**
     * Test kiểm tra giới hạn người tham gia
     */
    public function testMaxParticipants() {
        echo "<h3>Test 3: Giới hạn người tham gia</h3>";
        
        // Tạo sự kiện test với max_participants = 2
        $eventId = $this->eventModel->create([
            'title' => 'Test Event 3',
            'description' => 'Test Event Description 3',
            'event_date' => date('Y-m-d', strtotime('+7 days')),
            'event_time' => '10:00:00',
            'location' => 'Test Location 3',
            'max_participants' => 2,
            'created_by' => 1
        ]);
        
        // Đăng ký 2 người
        $result1 = $this->registrationModel->register($eventId, 4); // user3
        $result2 = $this->registrationModel->register($eventId, 5); // user4
        
        if ($result1 && $result2) {
            echo "<p style='color: green;'>✓ Đăng ký 2 người thành công</p>";
            
            // Thử đăng ký người thứ 3 (phải thất bại)
            $result3 = $this->registrationModel->register($eventId, 6); // user5
            if (!$result3) {
                echo "<p style='color: green;'>✓ Ngăn chặn đăng ký vượt quá giới hạn thành công</p>";
            } else {
                echo "<p style='color: red;'>✗ Không ngăn chặn được đăng ký vượt quá giới hạn</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Đăng ký 2 người thất bại</p>";
        }
        
        // Cleanup
        $this->db->execute("DELETE FROM registrations WHERE event_id = ?", [$eventId]);
        $this->db->execute("DELETE FROM events WHERE id = ?", [$eventId]);
        
        echo "<hr>";
    }
    
    /**
     * Test thống kê
     */
    public function testStats() {
        echo "<h3>Test 4: Thống kê</h3>";
        
        // Test thống kê theo sự kiện
        $statsByEvent = $this->registrationModel->getStatsByEvent();
        if (is_array($statsByEvent) && count($statsByEvent) > 0) {
            echo "<p style='color: green;'>✓ Lấy thống kê theo sự kiện thành công</p>";
            echo "<pre>";
            print_r($statsByEvent);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>✗ Lấy thống kê theo sự kiện thất bại</p>";
        }
        
        // Test thống kê theo người dùng
        $statsByUser = $this->registrationModel->getStatsByUser();
        if (is_array($statsByUser) && count($statsByUser) > 0) {
            echo "<p style='color: green;'>✓ Lấy thống kê theo người dùng thành công</p>";
            echo "<pre>";
            print_r($statsByUser);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>✗ Lấy thống kê theo người dùng thất bại</p>";
        }
        
        echo "<hr>";
    }
    
    /**
     * Chạy tất cả test
     */
    public function runAllTests() {
        $this->testRegister();
        $this->testUnregister();
        $this->testMaxParticipants();
        $this->testStats();
        
        echo "<h2>Hoàn thành Unit Test!</h2>";
    }
}

// Chạy test
$test = new RegistrationTest();
$test->runAllTests();
?>