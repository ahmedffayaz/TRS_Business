<?php

namespace App\Models;

use App\Enums\Project\ProjectIsAutoArchived;
use App\Enums\Project\ProjectNature;
use App\Enums\Project\ProjectStatus;
use App\Enums\Project\ProjectType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'client_id',
        'name',
        'reports_schedule',
        'client_email',
        'description',
        'start_date',
        'end_date',
        'status',
        'is_auto_archived',
        'budget',
        'hourly_rate',
        'currency',
        'type',
        'last_updated_at'
    ];

    protected $casts = [
        'type' => ProjectType::class,
        'is_auto_archived' => ProjectIsAutoArchived::class,
        'nature' => ProjectNature::class,
        'status' => ProjectStatus::class,
        'last_updated_at' => 'datetime',
    ];

    public function scopeGetList($query, $search, $columnName, $sortDirection) {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('budget', 'LIKE', '%' . $search . '%')
                    ->orWhere('type', 'LIKE', '%' . $search . '%')
                    ->orWhere('reports_schedule', 'LIKE', '%' . $search . '%');
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

    public function members(): BelongsToMany
    {
        return $this->BelongsToMany(User::class, 'project_members');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function openTasks(): HasMany
    {
        return $this->tasks()->whereNull('completed_at');
    }

    /**
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function closedTasks(): HasMany
    {
        return $this->tasks()->whereNotNull('completed_at');
    }

    public function closedBugs(): HasMany
    {
        return $this->tasks()->whereNotNull('completed_at');
    }

    /**
     * @return BelongsTo
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return MorphOne
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachment');
    }

    /**
     * @return HasManyThrough
     */
    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, Task::class);
    }

    /**
     * Return latest salary range
     * @return mixed
     */
    public function latestSalary()
    {
        return $this->salaries()->whereNull('end_date')->first();
    }

    /**
     * @return HasMany
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    /**
     * @return HasManyThrough
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(InvoicePayment::class, Invoice::class);
    }
}
