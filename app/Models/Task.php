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
		'completed_at'
	];

	/**
	 * @return BelongsTo
	 */
	public function project(): BelongsTo
	{
		return $this->belongsTo(Project::class)->withTrashed();
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
	public function billable_comments(): HasMany
	{
		return $this->hasMany(Comment::class)
			->where('type', 'time')->whereNull('invoiced_at');
	}

	/**
	 * @return MorphOne
	 */
	public function attachments(): MorphMany
	{
		return $this->morphMany(Attachment::class, 'attachment');
	}

	// Scopes
	public function scopeOpen($query)
	{
		return $query->whereNull('completed_at');
	}
}
