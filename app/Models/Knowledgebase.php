<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';

    protected $fillable = ['question', 'answer', 'keywords'];

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
}
