USE event_manager;

-- Xóa dữ liệu cũ (nếu có)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE registrations;
TRUNCATE TABLE events;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- Thêm tài khoản admin mặc định (mật khẩu: admin123)
INSERT INTO users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'Administrator', 'admin');

-- Thêm người dùng mẫu
INSERT INTO users (username, password, email, full_name, role) VALUES 
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user1@example.com', 'Nguyễn Văn A', 'user'),
('user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user2@example.com', 'Trần Thị B', 'user'),
('user3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user3@example.com', 'Lê Văn C', 'user'),
('user4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user4@example.com', 'Phạm Thị D', 'user'),
('user5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user5@example.com', 'Hoàng Văn E', 'user');

-- Thêm sự kiện mẫu
INSERT INTO events (title, description, event_date, event_time, location, max_participants, created_by) VALUES 
('Hội thảo Công nghệ 4.0', 'Hội thảo về xu hướng công nghệ 4.0 và tác động đến các ngành kinh tế', '2025-12-15', '09:00:00', 'Hội trường A, Tầng 5', 100, 1),
('Workshop Lập trình PHP', 'Hướng dẫn lập trình PHP cơ bản cho người mới bắt đầu', '2025-12-20', '14:00:00', 'Phòng Lab 1, Tầng 3', 30, 1),
('Ngày hội Việc làm 2025', 'Kết nối sinh viên với các doanh nghiệp uy tín', '2025-12-25', '08:30:00', 'Sân trường', 500, 1),
('Talkshow Khởi nghiệp', 'Chia sẻ kinh nghiệm khởi nghiệp từ các doanh nhân thành đạt', '2025-01-05', '19:00:00', 'Hội trường B', 200, 1),
('Cuộc thi Coding', 'Cuộc thi lập trình dành cho sinh viên CNTT', '2025-01-10', '13:00:00', 'Phòng Lab 2-3, Tầng 3', 50, 1),
('Hội thảo AI và Machine Learning', 'Tìm hiểu về Trí tuệ nhân tạo và Học máy', '2025-01-15', '09:30:00', 'Hội trường C', 150, 1),
('Workshop Thiết kế UI/UX', 'Hướng dẫn thiết kế giao diện người dùng thân thiện', '2025-01-20', '14:30:00', 'Phòng Thiết kế, Tầng 4', 25, 1);

-- Thêm dữ liệu đăng ký mẫu
INSERT INTO registrations (event_id, user_id, status) VALUES 
(1, 2, 'confirmed'),
(1, 3, 'confirmed'),
(1, 4, 'confirmed'),
(2, 2, 'confirmed'),
(2, 5, 'confirmed'),
(3, 2, 'confirmed'),
(3, 3, 'confirmed'),
(3, 4, 'confirmed'),
(3, 5, 'confirmed'),
(4, 3, 'confirmed'),
(4, 4, 'confirmed'),
(5, 2, 'confirmed'),
(5, 3, 'confirmed'),
(6, 4, 'confirmed'),
(6, 5, 'confirmed'),
(7, 2, 'confirmed'),
(7, 3, 'confirmed');

-- Thêm đăng ký của admin
INSERT INTO registrations (event_id, user_id, status) VALUES 
(1, 1, 'confirmed'),
(2, 1, 'confirmed'),
(3, 1, 'confirmed'),
(4, 1, 'confirmed'),
(5, 1, 'confirmed'),
(6, 1, 'confirmed'),
(7, 1, 'confirmed');

-- Thêm một số đăng ký bị hủy
INSERT INTO registrations (event_id, user_id, status) VALUES 
(2, 4, 'cancelled'),
(3, 2, 'cancelled'),
(5, 4, 'cancelled');