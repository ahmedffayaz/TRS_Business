<?php

namespace App\Models;

use App\Models\KnowledgeBase;
use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    protected $table = 'roles';
    public function contracts()
    {
        return $this->belongsToMany(TermsCondition::class);
    }

    public function knowledgebases(){
        return $this->belongsToMany(KnowledgeBase::class);
    }
}
