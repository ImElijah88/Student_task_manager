<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS student_task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $pdo->exec("USE student_task_manager");

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id    INT AUTO_INCREMENT PRIMARY KEY,
        username   VARCHAR(50)  NOT NULL UNIQUE,
        email      VARCHAR(100) NOT NULL UNIQUE,
        passkey    VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
        task_id     INT AUTO_INCREMENT PRIMARY KEY,
        user_id     INT          NOT NULL,
        title       VARCHAR(150) NOT NULL,
        description TEXT,
        priority    ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
        status      ENUM('Pending', 'In Progress', 'Complete') DEFAULT 'Pending',
        deadline    DATE,
        created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )");

    echo "Done. Database and tables created successfully.";
} catch (PDOException $e) {
    echo "Failed: " . $e->getMessage();
}
