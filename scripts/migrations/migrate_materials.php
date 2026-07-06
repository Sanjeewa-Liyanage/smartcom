<?php
require __DIR__ . '/../../config/database.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql1 = "CREATE TABLE IF NOT EXISTS class_topics (
        topic_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        class_id INT UNSIGNED NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    $pdo->exec($sql1);
    
    $sql2 = "CREATE TABLE IF NOT EXISTS materials (
        material_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        topic_id INT UNSIGNED NOT NULL,
        tutor_id INT UNSIGNED NOT NULL,
        title VARCHAR(200) NOT NULL,
        type ENUM('word', 'pdf', 'pptx', 'link', 'zip') NOT NULL,
        file_path VARCHAR(255) NULL,
        release_time DATETIME NULL,
        upload_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (topic_id) REFERENCES class_topics(topic_id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (tutor_id) REFERENCES tutors(tutor_id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    $pdo->exec($sql2);
    
    echo "Migration successful\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
