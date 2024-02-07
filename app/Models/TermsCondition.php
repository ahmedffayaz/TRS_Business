<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermsCondition extends Model
{
   protected $fillable =
      [  
         'uuid',
         'title',
         'description',
         'version',
         'company_id',
         'created_by',
         'updated_by',
      ];

      public function roles()
      {
         return $this->belongsToMany(Role::class);
      }
      public function createdByUser()
      {
          return $this->belongsTo(User::class, 'created_by');
      }
  
      public function updatedByUser()
      {
          return $this->belongsTo(User::class, 'updated_by');
      }
  
      public function company()
      {
          return $this->belongsTo(Company::class, 'company_id');
      }
}
