<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermsConditionUser extends Model
{
    protected $fillable =
    [
        'user_id',
        'contract_id',
        'uuid',
        'signature_url',
        'pdf_url',
    ];

    public function termsCondition()
    {
        return $this->belongsTo(TermsCondition::class);
    }
}
