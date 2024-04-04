<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'file',
        'conversion_rate',
        'remaining_amount',
        'bank',
        'bank_charges',
        'description',
        'billed_at'
    ];

	/**
	 * @return BelongsTo
	 */
	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}
}
