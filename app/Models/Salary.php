<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
