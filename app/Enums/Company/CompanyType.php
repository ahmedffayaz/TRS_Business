<?php

namespace App\Enums\Company;

enum CompanyType: string
{
    case PARENT = 'parent';
    case CHILD = 'child';
}
