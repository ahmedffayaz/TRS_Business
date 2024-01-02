<?php

namespace App\Enums\Project;

enum ProjectIsAutoArchived: int
{
    case ARCHIVED = 1;
    case UNARCHIVED = 0;
}
