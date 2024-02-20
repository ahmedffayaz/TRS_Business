<?php

namespace App\Enums\User;

enum AccountType : string
{
    case BUSINESS = 'business';
    case CLIENT = 'client';
}
