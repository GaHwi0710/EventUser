DROP DATABASE IF EXISTS `eventuser`;
CREATE DATABASE `eventuser` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `eventuser`;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#3498db',
  `icon` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `role` enum('user','admin','organizer') NOT NULL DEFAULT 'user',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `date` datetime NOT NULL,
  `time` time NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `timezone` varchar(50) DEFAULT 'Asia/Ho_Chi_Minh',
  `location` varchar(255) NOT NULL,
  `location_map` text DEFAULT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `organizer_name` varchar(100) DEFAULT NULL,
  `max_attendees` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'VND',
  `image` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','cancelled','postponed','Hiển thị') NOT NULL DEFAULT 'draft',
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `status` (`status`),
  KEY `featured` (`featured`),
  KEY `start_date` (`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TRIGGER IF EXISTS `before_events_insert`;
DELIMITER //
CREATE TRIGGER `before_events_insert`
BEFORE INSERT ON `events`
FOR EACH ROW
BEGIN
    SET NEW.date = NEW.start_date;
    SET NEW.time = TIME(NEW.start_date);
END;
//
DELIMITER ;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `event_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_title` varchar(255) NOT NULL,
  `tag_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_title` varchar(255) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `ticket_number` varchar(50) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `checked_in` tinyint(1) NOT NULL DEFAULT 0,
  `checked_in_time` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) DEFAULT NULL,
  `event_title` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'VND',
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_title` varchar(255) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories
INSERT INTO `categories` (`name`, `slug`, `description`, `color`, `icon`) VALUES
('Công nghệ', 'cong-nghe', 'Các sự kiện về công nghệ, lập trình, AI, blockchain...', '#3498db', 'bi-laptop'),
('Kinh doanh', 'kinh-doanh', 'Hội thảo về kinh doanh, khởi nghiệp...', '#2ecc71', 'bi-briefcase'),
('Văn hóa', 'van-hoa', 'Sự kiện cộng đồng, lễ hội...', '#e67e22', 'bi-people');

-- Users
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`, `email_verified`) VALUES
('admin', 'admin@eventuser.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 1, 1),
('organizer', 'organizer@eventuser.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Event Organizer', 'organizer', 1, 1);

-- Events
INSERT INTO `events`
(`title`, `slug`, `description`, `short_description`, `date`, `time`, `start_date`, `end_date`, `location`, `category_name`, `organizer_name`, `max_attendees`, `price`, `image`, `status`, `featured`)
VALUES
('Hội thảo Công nghệ 2025', 'hoi-thao-cong-nghe-2025', 'Xu hướng AI, Blockchain và công nghệ mới.', 'Khám phá công nghệ tiên phong năm 2025.', '2025-11-20 09:00:00', '09:00:00', '2025-11-20 09:00:00', '2025-11-20 17:00:00', 'Hà Nội', 'Công nghệ', 'EventUser', 100, 0.00, 'tech.jpg', 'published', 1),
('Workshop Marketing 2025', 'workshop-marketing-2025', 'Chiến lược quảng cáo online hiệu quả.', 'Workshop dành cho marketer hiện đại.', '2025-12-05 14:00:00', '14:00:00', '2025-12-05 14:00:00', '2025-12-05 18:00:00', 'TP.HCM', 'Kinh doanh', 'EventUser', 50, 299000.00, 'marketing.jpg', 'published', 0),
('Sự kiện Trung Thu 2025', 'su-kien-trung-thu-2025', 'Sự kiện dành cho cộng đồng và trẻ em.', 'Đêm Trung Thu ấm áp bên gia đình.', '2025-09-10 18:00:00', '18:00:00', '2025-09-10 18:00:00', '2025-09-10 21:00:00', 'Hồ Gươm', 'Văn hóa', 'EventUser', 200, 0.00, 'trungthu.jpg', 'published', 0);

-- Tags
INSERT INTO `tags` (`name`, `slug`) VALUES
('AI', 'ai'),
('Marketing', 'marketing');

-- Event_Tags
INSERT INTO `event_tags` (`event_title`, `tag_name`) VALUES
('Hội thảo Công nghệ 2025', 'AI'),
('Workshop Marketing 2025', 'Marketing');

-- Registrations
INSERT INTO `registrations` (`event_title`, `user_email`, `status`, `ticket_number`) VALUES
('Hội thảo Công nghệ 2025', 'user1@example.com', 'approved', 'EVT0001'),
('Workshop Marketing 2025', 'user2@example.com', 'pending', 'EVT0002');

-- Reviews
INSERT INTO `reviews` (`event_title`, `user_email`, `rating`, `title`, `comment`, `status`) VALUES
('Hội thảo Công nghệ 2025', 'user1@example.com', 5, 'Rất tuyệt vời', 'Nội dung hấp dẫn, diễn giả chuyên nghiệp.', 'approved');

-- Notifications
INSERT INTO `notifications` (`user_email`, `title`, `message`, `type`, `is_read`) VALUES
('user1@example.com', 'Đăng ký thành công', 'Bạn đã tham gia sự kiện Hội thảo Công nghệ 2025.', 'success', 1);

-- Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'EventUser', 'Tên website'),
('site_email', 'contact@eventuser.vn', 'Email liên hệ'),
('timezone', 'Asia/Ho_Chi_Minh', 'Múi giờ hệ thống');
