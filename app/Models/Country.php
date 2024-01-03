<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

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
