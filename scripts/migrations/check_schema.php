<?php
require __DIR__ . '/../../config/database.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SHOW CREATE TABLE classes');
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
    
    $stmt = $pdo->query('SHOW CREATE TABLE tutors');
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
