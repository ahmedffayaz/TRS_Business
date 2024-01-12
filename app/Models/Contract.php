<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
   protected $fillable =
      [  
         'uuid',
         'title',
         'description',
         'version',
      ];

      public function roles()
      {
         return $this->belongsToMany(Role::class);
      }
}
