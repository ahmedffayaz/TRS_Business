<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';

    protected $fillable = ['question', 'answer', 'keywords'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
