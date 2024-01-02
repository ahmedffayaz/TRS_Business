<?php

namespace App\Models;

use App\Enums\Comment\CommentType;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Comment\CommentIsBillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'description',
        'time',
        'type',
        'to',
        'from',
        'dated',
        'invoiced_at',
        'is_billable'
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => CommentType::class,
        'invoiced_at' => 'datetime',
        'is_billable' => CommentIsBillable::class,
    ];
}
