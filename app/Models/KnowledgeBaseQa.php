<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeBaseQa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = array(
        'knowledge_base_topic_id',
        'question',
        'slug',
        'answer',
        'keywords'
    );

    public function topic() : BelongsTo {
        return $this->belongsTo(KnowledgeBaseTopic::class, 'knowledge_base_topic_id', 'id');
    }
}
