<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database — PDO Singleton
 *
 * Usage:
 *   $db = Database::getInstance();
 *   $rows = $db->query('SELECT * FROM users WHERE role = ?', ['student'])->fetchAll();
 */
class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // In development, show the error; in production, log it.
            if (APP_ENV === 'development') {
                die('<h2 style="font-family:sans-serif;color:#ef4444;padding:2rem">
                    Database Connection Failed: ' . htmlspecialchars($e->getMessage()) . '
                     <br><small>Check config/database.php and ensure MySQL is running.</small>
                </h2>');
            }
            die('Service temporarily unavailable. Please try again later.');
        }
    }

    /** Returns the singleton instance */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Execute a prepared statement and return the PDOStatement */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Return the underlying PDO object (for lastInsertId, transactions, etc.) */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
