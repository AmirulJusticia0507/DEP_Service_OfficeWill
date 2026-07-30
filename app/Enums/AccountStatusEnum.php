<?php

namespace App\Enums;

enum AccountStatusEnum: string
{
    case ACTIVE = 'ACTIVE';
    case LOCKED = 'LOCKED';
    case INACTIVE = 'INACTIVE';
}
