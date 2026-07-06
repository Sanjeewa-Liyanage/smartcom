<?php

namespace App\Models;

use App\Core\Model;

class Topic extends Model
{
    protected string $table = 'class_topics';

    protected function getPrimaryKey(): string
    {
        return 'topic_id';
    }

    /**
     * Get all topics for a specific class.
     */
    public function getTopicsByClass(int $classId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE class_id = ? ORDER BY created_at ASC";
        return $this->db->query($sql, [$classId])->fetchAll();
    }
}
