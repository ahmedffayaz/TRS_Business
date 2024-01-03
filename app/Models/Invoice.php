<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'file',
        'invoice_number',
        'currency',
        'total',
        'deduction',
        'notes',
        'due_at',
        'billed_at',
        'paid_amount',
        'status',
        'send_emails'
    ];

    /**
	 * @return BelongsTo
	 */
	public function project(): BelongsTo
	{
		return $this->belongsTo(Project::class)->withTrashed();
	}

    /**
	 * @return HasMany
	 */
	public function invoice_data(): HasMany {
		return $this->hasMany(InvoiceData::class);
	}

    /**
     * @return HasMany
     */
    public function invoice_payments() : HasMany
	{
		return $this->hasMany(InvoicePayment::class);
	}
}
