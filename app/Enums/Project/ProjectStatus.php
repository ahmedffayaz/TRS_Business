<?php

namespace App\Enums\Project;

enum ProjectStatus : string
{
    case PENDING = 'pending';
    case INPROGRESS = 'in-progress';
    case DELIVERED = 'delivered';
}