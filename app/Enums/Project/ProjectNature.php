<?php

namespace App\Enums\Project;

enum ProjectNature: string
{
    case FIXED = 'fixed';
    case WEEKLY = 'hourly';
    case MONTHLY = 'monthly';
}
