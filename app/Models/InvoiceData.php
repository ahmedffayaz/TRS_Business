<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceData extends Model
{
    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['invoice_id', 'task_id', 'comments', 'time', 'rate_per_hour', 'amount'];

	/**
	 * @return BelongsTo
	 */
	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}

	/**
	 * @return BelongsTo
	 */
	public function task(): BelongsTo
	{
		return $this->belongsTo(Task::class);
	}
}
