<?php

namespace App\Models;

use App\Enums\Leave\LeaveIsWorking;
use App\Enums\Leave\LeaveStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
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

    protected $casts = [
        'is_working' => LeaveIsWorking::class,
        'status' => LeaveStatus::class
    ];
    
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}