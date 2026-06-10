<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    protected $table = 'task_logs';
    
    protected $fillable = [
        'task_id',
        'old_priority',
        'new_priority',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
