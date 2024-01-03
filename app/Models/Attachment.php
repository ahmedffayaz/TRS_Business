<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'file', 'mimes', 'attachment_id', 'attachment_type',
    ];

    public function attachment()
    {
        return $this->morphTo();
    }
}
