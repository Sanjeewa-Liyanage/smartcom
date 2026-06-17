<?php

namespace App\Core;

/**
 * Model — Base Model
 *
 * Provides generic CRUD methods for any database table.
 * Child classes must set $table and override getPrimaryKey() if needed.
 *
 * Usage:
 *   class User extends Model {
 *       protected string $table = 'users';
 *       protected function getPrimaryKey(): string { return 'user_id'; }
 *   }
 */
abstract class Model
{
    protected Database $db;

    /** Database table name — must be set in each child class */
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    /** Find a single record by primary key. Returns null if not found. */
    public function find(int $id): ?array
    {
        $pk  = $this->getPrimaryKey();
        $row = $this->db->query(
            "SELECT * FROM `{$this->table}` WHERE `{$pk}` = ? LIMIT 1",
            [$id]
        )->fetch();

        return $row ?: null;
    }

    /** Find a single record matching a column value. Returns null if not found. */
    public function findBy(string $column, mixed $value): ?array
    {
        $row = $this->db->query(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1",
            [$value]
        )->fetch();

        return $row ?: null;
    }

    /** Return all rows, optionally sorted */
    public function findAll(string $orderBy = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy !== '') {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Insert a new row.
     *
     * @param array $data  Associative array of column => value
     * @return int         The new record's primary key value
     */
    public function create(array $data): int
    {
        $cols   = implode('`, `', array_keys($data));
        $places = implode(', ', array_fill(0, count($data), '?'));

        $this->db->query(
            "INSERT INTO `{$this->table}` (`{$cols}`) VALUES ({$places})",
            array_values($data)
        );

        return (int) $this->db->getPdo()->lastInsertId();
    }

    /**
     * Update a row by its primary key.
     *
     * @param int   $id    Primary key value of the row to update
     * @param array $data  Associative array of column => new value
     */
    public function update(int $id, array $data): void
    {
        $pk  = $this->getPrimaryKey();
        $set = implode(' = ?, ', array_map(
            static fn($col) => "`{$col}`",
            array_keys($data)
        )) . ' = ?';

        $this->db->query(
            "UPDATE `{$this->table}` SET {$set} WHERE `{$pk}` = ?",
            [...array_values($data), $id]
        );
    }

    /** Delete a row by primary key */
    public function delete(int $id): void
    {
        $pk = $this->getPrimaryKey();
        $this->db->query(
            "DELETE FROM `{$this->table}` WHERE `{$pk}` = ?",
            [$id]
        );
    }

    /** Count all rows in the table */
    public function count(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM `{$this->table}`"
        )->fetchColumn();
    }

    // ── Override in Child Classes ─────────────────────────────────────────────

    /** Return the name of the primary key column */
    protected function getPrimaryKey(): string
    {
        return 'id';
    }
}
