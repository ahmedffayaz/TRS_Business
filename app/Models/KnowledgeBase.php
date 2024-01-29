<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KnowledgeBase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = array(
        'created_by',
        'updated_by',
        'name',
        'slug',
        'description',
        'image'
    );

    public function scopeGetList($query, $search, $columnName, $sortDirection) {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('budget', 'LIKE', '%' . $search . '%')
                    ->orWhere('rate_per_hour', 'LIKE', '%' . $search . '%')
                    ->orWhere('rate_unit', 'LIKE', '%' . $search . '%')
                    ->orWhere('type', 'LIKE', '%' . $search . '%')
                    ->orWhere('reports_schedule', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function topics() : HasMany
    {
        return $this->hasMany(KnowledgeBaseTopic::class);
    }

    public function companies() : BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }
}
