<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid', 'name', 'tmpFilename', 'file', 'mimes', 'size', 'attachmentable_id', 'attachmentable_type',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($query) {
            $query->uuid = !empty($query->uuid) ? $query->uuid : getUuid();
        });
    }

    public function attachmentable()
    {
        return $this->morphTo();
    }
}
