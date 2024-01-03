<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'converted_amount',
        'remaining_amount',
        'bank',
        'bank_charges',
        'conversion_rate',
        'billed_at',
        'description'
    ];

	/**
	 * @return BelongsTo
	 */
	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}
}
