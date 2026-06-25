<?php

namespace App\Models;

use App\Core\Model;

class Subject extends Model
{
    protected string $table = 'subjects';

    protected function getPrimaryKey(): string
    {
        return 'subject_id';
    }
}
