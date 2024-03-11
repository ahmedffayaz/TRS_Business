<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermsConditionUser extends Model
{
    protected $fillable =
    [
        'user_id',
        'terms_condition_id',
        'uuid',
        'signature_url',
        'pdf_url'
    ];

    public function scopeGetList($query, $search, $columnName, $sortDirection)
    {
        if (!empty($search)) {
            $query->whereHas('termsCondition', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'LIKE', '%' . $search . '%')
                        ->orWhere('description', 'LIKE', '%' . $search . '%');
                });
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

    public function termsCondition() : BelongsTo
    {
        return $this->belongsTo(TermsCondition::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
