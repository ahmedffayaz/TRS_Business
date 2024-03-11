<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermsConditionUser extends Model
{
    protected $fillable =
    [
        'user_id',
        'terms_condition_id',
        'uuid',
        'signature_url',
        'pdf_url'
    ];

    public function termsCondition()
    {
        return $this->belongsTo(TermsCondition::class);
    }
}
