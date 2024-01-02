<?php

namespace App\Models;

use App\Enums\Project\ProjectIsAutoArchived;
use App\Enums\Project\ProjectNature;
use App\Enums\Project\ProjectType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'client_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'description',
        'budget',
        'rate_per_hour',
        'rate_unit',
        'type',
        'is_auto_archived',
        'nature',
        'reports_schedule',
        'last_updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => ProjectType::class,
        'is_auto_archived' => ProjectIsAutoArchived::class,
        'nature' => ProjectNature::class,
        'last_updated_at' => 'datetime',
    ];

    public function scopeGetList($query, $search, $columnName, $sortDirection)
    {
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
}
