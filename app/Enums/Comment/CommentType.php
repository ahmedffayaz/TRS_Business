<?php

namespace App\Enums\Comment;

enum CommentType: string
{
    case ASSIGNED = 'assigned';
    case REMOVED = 'removed';
    case COMMENT = 'comment';
    case TIME = 'time';
    case ATTACHMENT = 'attachment';
}
