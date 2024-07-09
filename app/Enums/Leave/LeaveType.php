<?php

namespace App\Enums\Leave;

enum LeaveType : string
{
    case WORK_FROM_HOME = 'work from home';
    case HALF_LEAV = 'half leave';
    case LEAVE = 'leave';
}
