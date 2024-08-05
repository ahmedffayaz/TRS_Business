<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'favicon',
        'address',
        'city',
        'postal_code',
        'country_id',
        'invoice_prefix',
        'invoice_serial',
        'date_format'
    ];

    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function clients() : HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function admin():HasOne
    {
        return $this->hasOne(User::class);
    }
    public function businessRoles()
    {
        return $this->belongsToMany(Role::class, 'business_role', 'business_id', 'role_id');
    }
}
