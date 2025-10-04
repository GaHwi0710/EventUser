-- database/schema.sql
-- Tạo database (nếu chưa có)
CREATE DATABASE IF NOT EXISTS eventuser
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE eventuser;

-- USERS
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(120) UNIQUE,
  phone         VARCHAR(20) UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  name          VARCHAR(120) DEFAULT NULL,
  role          ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at    DATETIME NOT NULL,
  INDEX (email),
  INDEX (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- EVENTS
DROP TABLE IF EXISTS events;
CREATE TABLE events (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(255) NOT NULL,
  description MEDIUMTEXT,
  location    VARCHAR(255),
  image_url   VARCHAR(255),
  start_time  DATETIME NOT NULL,
  end_time    DATETIME NOT NULL,
  status      ENUM('active','draft','closed') NOT NULL DEFAULT 'active',
  created_by  INT NOT NULL,
  created_at  DATETIME NOT NULL,
  INDEX (status),
  INDEX (start_time),
  INDEX (created_by),
  CONSTRAINT fk_events_user
    FOREIGN KEY (created_by) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- EVENT_TICKETS (tuỳ chọn nhưng được dùng để hiển thị "Giá từ ...")
DROP TABLE IF EXISTS event_tickets;
CREATE TABLE event_tickets (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  event_id  INT NOT NULL,
  name      VARCHAR(120) NOT NULL,
  price     DECIMAL(12,2) NOT NULL DEFAULT 0,
  quantity  INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_tickets_event
    FOREIGN KEY (event_id) REFERENCES events(id)
    ON DELETE CASCADE,
  INDEX (event_id),
  INDEX (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- REGISTRATIONS (Vé của tôi / lịch sử đăng ký)
DROP TABLE IF EXISTS registrations;
CREATE TABLE registrations (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  event_id   INT NOT NULL,
  ticket_id  INT NULL,
  quantity   INT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_reg_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_reg_event
    FOREIGN KEY (event_id) REFERENCES events(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_reg_ticket
    FOREIGN KEY (ticket_id) REFERENCES event_tickets(id)
    ON DELETE SET NULL,
  INDEX (user_id),
  INDEX (event_id),
  INDEX (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
