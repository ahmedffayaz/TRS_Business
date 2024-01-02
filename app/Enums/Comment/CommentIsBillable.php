<?php

namespace App\Enums\Comment;

enum CommentIsBillable: int
{
    case BILLABLE = 1;
    case NON_BILLABLE = 0;
}
