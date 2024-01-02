<?php

namespace App\Models;

use App\Enums\Leave\LeaveIsWorking;
use App\Enums\Leave\LeaveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'reason',
        'start_date',
        'end_date',
        'is_working',
        'status',
        'processed_by',
        'processing_reason'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_working' => LeaveIsWorking::class,
        'status' => LeaveStatus::class
    ];
}
