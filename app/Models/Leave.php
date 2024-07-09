<?php

namespace App\Models;

use App\Enums\Leave\LeaveType;
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
        'business_id',
        'status',
        'processed_by',
        'processing_reason',
    ];

    protected $casts = [
        'is_working' => LeaveType::class,
        'status' => LeaveStatus::class
    ];

    public function scopeSessionBusiness()
    {
        return $this->whereHas('business', function ($query) {
            $query->whereName(session('business'));
        });
    }

    public function scopeGetList($query, $search, $columnName, $sortDirection)
    {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('start_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('end_date', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

    /**
     * @return BelongsTo
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
