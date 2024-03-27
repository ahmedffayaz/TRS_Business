<?php

namespace App\Models;

use App\Enums\Invoice\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'invoice_number',
        'file',
        'currency',
        'total',
        'deduction',
        'notes',
        'due_at',
        'billed_at',
        'status',
        'send_emails',
        'paid_amount'
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
    ];

    public function scopeGetList($query, $search, $columnName, $sortDirection) {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('invoice_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%')
                    ->orWhere('notes', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('project', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('client', function ($query) use ($search) {
                            $query->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhereHas('business', function ($query) use ($search) {
                                $query->where('invoice_number', 'LIKE', '%' . $search . '%');
                            });
                        });
                    });
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

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
	public function invoiceData(): HasMany {
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
