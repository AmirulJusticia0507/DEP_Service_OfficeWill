<?php

namespace App\Enums;

enum EnrollmentStatusEnum: string
{
    case ENROLLED = 'ENROLLED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}
