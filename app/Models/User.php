<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Enums\User\AccountType;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'email',
        'alternative_email',
        'password',
        'client_id',
        'account_type',
        'designation',
        'phone',
        'address',
        'device_token',
        'avatar',
        'alternative_number',
        'salary',
        'currency',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'account_type' => AccountType::class
    ];

    public function accountType(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => !empty($this->account_type) ? AccountType::BUSINESS : AccountType::CLIENT
        );
    }

    /**
     * Encrypt password
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        return $this->attributes['password'] = bcrypt($value);
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getNameWithDesignationAttribute()
    {
        return $this->first_name . ' ' . $this->last_name . ' -- ' . slugToName(implode(', ', $this->getRoleNames()->toArray()));
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function commentsTo(): HasMany
    {
        return $this->hasMany(Comment::class, 'to', 'id');
    }

    /**
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return HasMany
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    public function userContracts(): HasMany
    {
        return $this->hasMany(TermsConditionUser::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopeGetList($query, $search, $columnName, $sortDirection)
    {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('alternative_email', 'LIKE', '%' . $search . '%')
                    ->orWhere('designation', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('alternative_number', 'LIKE', '%' . $search . '%')
                    ->orWhere('address', 'LIKE', '%' . $search . '%')
                    ->orWhere('salary', 'LIKE', '%' . $search . '%')
                    ->orWhere('alternative_number', 'LIKE', '%' . $search . '%')
                    ->status($search);
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

    public function scopeStatus($query, $keyword)
    {
        if ($keyword === 'active' || $keyword === 'Active' || $keyword === 'ACTIVE') {
            return $query->orWhere('is_active', 1);
        }

        if ($keyword === 'inactive' || $keyword === 'Inactive' || $keyword === 'INACTIVE') {
            return $query->orWhere('is_active', 0);
        }
    }

    public function getStatusAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function scopeSessionBusiness($query)
    {
        return $query->whereHas('business', function ($query) {
            $query->whereName(session('business'));
        });
    }

    public function scopeUsersWithoutClientRole()
    {
        return $this->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'client');
            });
    }

    public function getAvatarNameAttribute()
    {
        return Str::upper(Str::substr($this->first_name, 0, 1)) . Str::upper(Str::substr($this->last_name, 0, 1));
    }
}
