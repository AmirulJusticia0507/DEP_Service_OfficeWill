<?php

namespace App\Enums;

enum AuthorityScopeEnum: string
{
    case AFFILIATION_ONLY = 'ONLY';
    case BELOW = 'BELOW';
    case ALL = 'ALL';
}
