<?php

namespace App\Enums\Task;

enum TaskPriority : string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
