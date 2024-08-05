<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
	use SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'description',
		'user_id',
		'project_id',
		'priority',
		'start_date',
		'end_date',
		'completed_at',
        'billed_at'
	];

	protected $casts = [
        'completed_at' => 'datetime',
        'billed_at' => 'datetime',
    ];

    public function scopeGetList($query, $search, $columnName, $sortDirection) {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('priority', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

	/**
	 * @return BelongsTo
	 */
	public function project(): BelongsTo
	{
		return $this->belongsTo(Project::class);
	}

    public function scopeHasProject($query, $projectId = null)
    {
        return $query->when($projectId, function ($query) use ($projectId) {
            $query->whereHas('project', function ($query) use ($projectId) {
                $query->sessionBusiness()->whereId($projectId);
            });
        })->whereHas('project', function ($query) {
            if (!auth()->user()->hasRole('super-admin')) {
                $query->sessionBusiness()->whereHas('members', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                });
            } else {
                $query->sessionBusiness();
            }
        });
    }

	/**
	 * @return BelongsTo
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return HasMany
	 */
	public function comments(): HasMany
	{
		return $this->hasMany(Comment::class);
	}

	/**
	 * Return only billable comments
	 * @return HasMany
	 */
	public function comments_with_time(): HasMany
	{
		return $this->hasMany(Comment::class)
			->where('type', 'time');
	}

	/**
	 * Return only billable comments
	 * @return HasMany
	 */
	public function billableComments(): HasMany
	{
		return $this->hasMany(Comment::class)
			->where('type', 'time')->whereNull('invoiced_at');
	}

	/**
	 * @return MorphOne
	 */
// In the model that has many attachments
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }


	// Scopes
	public function scopeOpen($query)
	{
		return $query->whereNull('completed_at');
	}
}
