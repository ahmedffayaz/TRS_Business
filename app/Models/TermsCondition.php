<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermsCondition extends Model
{
   protected $fillable =
    [
        'uuid',
        'title',
        'description',
        'version',
        'business_id',
        'created_by',
        'updated_by',
    ];

    public function scopeGetList($query, $search, $columnName, $sortDirection) {
        if (!empty($search)) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->orderBy($columnName, $sortDirection);
    }

    public function scopeSessionBusiness($query)
    {
        return $query->whereHas('business', function ($query) {
            $query->whereName(session('business'));
        });
    }

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

      public function business() : BelongsTo
      {
          return $this->belongsTo(Business::class);
      }
}
