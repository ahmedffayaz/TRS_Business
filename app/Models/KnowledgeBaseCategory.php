<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KnowledgeBaseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'created_by',
        'updated_by',
        'name',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function scopeGetList($query, $search, $columnName, $sortDirection) {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', '%' . $search . '%');
            })->orWhereHas('questions', function ($query) use ($search) {
                $query->where('question', 'LIKE', '%' . $search . '%')
                ->orWhereHas('keywords', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

    public function scopeSessionBusiness($query)
    {
        return $query->whereHas('business', function ($query) {
            $query->whereName(session('business'));
        });
    }

    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function questions() : HasMany
    {
        return $this->hasMany(KnowledgeBase::class);
    }
}
