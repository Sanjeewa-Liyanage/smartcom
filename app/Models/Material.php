<?php

namespace App\Models;

use App\Core\Model;

class Material extends Model
{
    protected string $table = 'materials';

    protected function getPrimaryKey(): string
    {
        return 'material_id';
    }

    /**
     * Get all materials for a specific topic.
     */
    public function getMaterialsByTopic(int $topicId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE topic_id = ? ORDER BY upload_date ASC";
        return $this->db->query($sql, [$topicId])->fetchAll();
    }
}
