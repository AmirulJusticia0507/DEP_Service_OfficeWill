<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Employee;

class CoursePolicy
{
    public function viewAny(Employee $user): bool
    {
        return $user->hasPermission('can_register_course');
    }

    public function view(Employee $user, Course $course): bool
    {
        return $user->hasPermission('can_register_course');
    }

    public function create(Employee $user): bool
    {
        return $user->hasPermission('can_register_course');
    }

    public function update(Employee $user, Course $course): bool
    {
        return $user->hasPermission('can_register_course');
    }

    public function delete(Employee $user, Course $course): bool
    {
        return $user->hasPermission('can_register_course');
    }
}
