<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'official_name',
        'continent_name',
        'alpha_2_code',
        'alpha_3_code',
        'numeric_code',
        'country_code',
        'official_language'
    ];
}
