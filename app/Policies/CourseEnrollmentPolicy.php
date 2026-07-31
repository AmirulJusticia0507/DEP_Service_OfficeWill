<?php

namespace App\Policies;

use App\Models\CourseEnrollment;
use App\Models\Employee;

class CourseEnrollmentPolicy
{
    public function complete(Employee $user, CourseEnrollment $enrollment): bool
    {
        return $enrollment->employee_id === $user->id;
    }

    public function sendConfirmation(Employee $user, CourseEnrollment $enrollment): bool
    {
        return $user->hasPermission('can_register_course');
    }
}
