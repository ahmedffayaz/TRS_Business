<?php

namespace App\Enums\Project;

enum ProjectIsAutoArchived: int
{
    case ARCHIVE = 1;
    case UNARCHIVE = 0;
}
