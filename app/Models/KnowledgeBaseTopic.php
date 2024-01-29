<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeBaseTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = array('knowledge_base_id', 'name', 'slug');

    public function knowledgeBase() : BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function qas() : HasMany
    {
        return $this->hasMany(KnowledgeBaseQa::class);
    }

    public function qa() : HasOne
    {
        return $this->hasOne(KnowledgeBaseQa::class);
    }
}
