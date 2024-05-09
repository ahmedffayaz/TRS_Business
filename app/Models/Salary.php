<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'start_date',
        'end_date',
        'salary',
        'salary_per_hour'
    ];

    public function project() : BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
