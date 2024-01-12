<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Comment extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'description', 'time', 'not_billable_time', 'type', 'task_id', 'to', 'from', 'invoiced_at', 'dated'
	];

	/**
	 * @return BelongsTo
	 */
	public function from_user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'from', 'id');
	}

	/**
	 * @return BelongsTo
	 */
	public function to_user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'to', 'id');
	}

	/**
	 * @return BelongsTo
	 */
	public function task(): BelongsTo
	{
		return $this->belongsTo(Task::class);
	}

	/**
	 * @return MorphOne
	 */
	public function attachment(): MorphOne
	{
		return $this->morphOne(Attachment::class, 'attachment');
	}
}
