<?php

namespace App\Models;

use App\Enums\Company\CompanyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'logo',
        'name',
        'address',
        'city',
        'postal_code',
        'country',
        'rate_per_hour',
        'rate_per_hour_unit',
        'type',
        'parent_id',
        'invoice_prefix',
        'invoice_serial'
    ];

	/**
	 * Return company name with type for listing
	 *
	 * @return string
	 */
	public function getNameWithTypeAttribute() {
		return $this->name . ' - ' . $this->type;
	}
	protected $casts = [
        'type' => CompanyType::class,
    ];

    public function scopeGetList($query, $search, $columnName, $sortDirection)
    {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('street_address', 'LIKE', '%' . $search . '%')
                    ->orWhere('city', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('country', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhere('postal_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('type', 'LIKE', '%' . $search . '%')
                    ->orWhere('invoice_prefix', 'LIKE', '%' . $search . '%')
                    ->orWhere('invoice_serial', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

    public function type(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => !empty($this->parent_id) ? CompanyType::CHILD : CompanyType::PARENT
        );
    }

    public function employees(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
	
	public function users(): HasMany
	{
		return $this->hasMany(User::class);
	}

	public function projects(): HasMany
	{
		return $this->hasMany(Project::class);
	}
	public function invoices()
	{
		return $this->hasManyThrough('App\Invoice', 'App\Project');
	}

	/**
	 * @return BelongsTo
	 */
	public function company(): BelongsTo {
		return $this->belongsTo(Company::class, 'parent_id', 'id');
	}
}
