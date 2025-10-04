USE eventuser;

-- USERS
INSERT INTO users (email, phone, password_hash, name, role, created_at) VALUES
('admin@example.com', NULL, '$2y$10$uGmYxFfnhwG9e0zZcEoQkuFzVwGQ.7Gg7mV4wE2dYk3w3V2aYz0qK', 'Admin', 'admin', NOW()),
('user@example.com',  NULL, '$2y$10$uGmYxFfnhwG9e0zZcEoQkuFzVwGQ.7Gg7mV4wE2dYk3w3V2aYz0qK', 'Demo User', 'user', NOW());
-- Ghi chú: hash trên là ví dụ bcrypt cho mật khẩu "123456". Bạn có thể thay bằng hash bạn tự generate.

-- EVENTS
INSERT INTO events (title, description, location, image_url, start_time, end_time, status, created_by, created_at) VALUES
('Tech Talk 2025', 'Sự kiện chia sẻ công nghệ.', 'Hà Nội', 'assets/images/sample1.jpg', DATE_ADD(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY + 2 HOUR), 'active', 1, NOW()),
('EDU Fair',       'Ngày hội giáo dục.',        'TP. HCM', 'assets/images/sample2.jpg', DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 7 DAY + 4 HOUR), 'active', 1, NOW()),
('Music Night',    'Đêm nhạc sinh viên.',       'Đà Nẵng', 'assets/images/sample3.jpg', DATE_ADD(NOW(), INTERVAL 10 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY + 2 HOUR), 'draft',  2, NOW());

-- EVENT_TICKETS
INSERT INTO event_tickets (event_id, name, price, quantity) VALUES
(1, 'Standard',  99000, 200),
(1, 'VIP',      199000,  50),
(2, 'Thường',    0,     500),
(3, 'Ghế ngồi',  49000, 300);

-- REGISTRATIONS (user id=2)
INSERT INTO registrations (user_id, event_id, ticket_id, quantity, created_at) VALUES
(2, 1, 1, 2, NOW()),
(2, 2, 3, 1, NOW());
