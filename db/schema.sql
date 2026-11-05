-- Student Task Manager — Database Schema
-- Run this in phpMyAdmin after creating the database: student_task_manager

CREATE TABLE IF NOT EXISTS users (
    user_id    INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    passkey    VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasks (
    task_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    title       VARCHAR(150) NOT NULL,
    description TEXT,
    priority    ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    status      ENUM('Pending', 'In Progress', 'Complete') DEFAULT 'Pending',
    deadline    DATE,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
