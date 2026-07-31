<?php

namespace App\Policies;

use App\Models\Employee;
use App\Services\ScopeService;

class EmployeePolicy
{
    public function __construct(
        private ScopeService $scope
    ) {}

    public function viewAny(Employee $user): bool
    {
        return $user->hasPermission('can_register_employee');
    }

    public function view(Employee $user, Employee $employee): bool
    {
        return $user->hasPermission('can_register_employee')
            && $this->scope->canAccessEmployee($user, $employee);
    }

    public function create(Employee $user): bool
    {
        return $user->hasPermission('can_register_employee');
    }

    public function update(Employee $user, Employee $employee): bool
    {
        return $user->hasPermission('can_register_employee')
            && $this->scope->canAccessEmployee($user, $employee);
    }

    public function delete(Employee $user, Employee $employee): bool
    {
        return $user->hasPermission('can_register_employee')
            && $this->scope->canAccessEmployee($user, $employee)
            && $employee->id !== $user->id;
    }

    public function viewAuthority(Employee $user): bool
    {
        return $user->isAdmin();
    }

    public function manageAuthority(Employee $user, Employee $employee): bool
    {
        return $user->isAdmin()
            && $this->scope->canAccessEmployee($user, $employee)
            && $employee->id !== $user->id;
    }
}
