<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeBase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'knowledge_base_category_id',
        'question',
        'answer',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseCategory::class, 'knowledge_base_category_id');
    }

    public function keywords() : BelongsToMany
    {
        return $this->belongsToMany(Keyword::class);
    }
}
