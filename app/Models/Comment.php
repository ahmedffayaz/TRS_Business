<?php

namespace App\Models;

use App\Enums\Comment\CommentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

    protected $casts = [
        "type" => CommentType::class,
    ];


	protected $fillable = [
		'description', 'time', 'type', 'task_id', 'to', 'from', 'dated', 'invoiced_at', 'is_billable', 'deleted_at'
	];

    public function scopeGetList($query, $search, $columnName, $sortDirection) {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('description', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

	/**
	 * @return BelongsTo
	 */
	public function fromUser(): BelongsTo
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
