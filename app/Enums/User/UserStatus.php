<?php

namespace App\Enums\User;

enum UserStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
}
