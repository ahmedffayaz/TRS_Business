<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermConditionUser extends Model
{
    protected $fillable =
    [
        'user_id',
        'contract_id',
        'uuid',
        'signature_url',
        'pdf_url',
    ];

    public function contract()
    {
        return $this->belongsTo(TermsCondition::class);
    }
}
    