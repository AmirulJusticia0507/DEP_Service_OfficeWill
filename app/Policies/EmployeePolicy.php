<?php

namespace App\Policies;

use App\Models\Employee;

class EmployeePolicy
{
    public function viewAny(Employee $user): bool
    {
        return $user->hasPermission('can_register_employee');
    }

    public function view(Employee $user, Employee $employee): bool
    {
        return $user->hasPermission('can_register_employee')
            && $employee->company_id === $user->company_id;
    }

    public function create(Employee $user): bool
    {
        return $user->hasPermission('can_register_employee');
    }

    public function update(Employee $user, Employee $employee): bool
    {
        return $user->hasPermission('can_register_employee')
            && $employee->company_id === $user->company_id;
    }

    public function delete(Employee $user, Employee $employee): bool
    {
        return $user->hasPermission('can_register_employee')
            && $employee->company_id === $user->company_id
            && $employee->id !== $user->id;
    }

    public function viewAuthority(Employee $user): bool
    {
        return $user->isAdmin();
    }

    public function manageAuthority(Employee $user, Employee $employee): bool
    {
        return $user->isAdmin()
            && $employee->company_id === $user->company_id
            && $employee->id !== $user->id;
    }
}
