<?php

namespace App\Services;

use App\Models\Employee;
use App\Actions\Employee\FilterEmployeeByScopeAction;
use Illuminate\Database\Eloquent\Builder;

class ScopeFilterService
{
    public function __construct(
        private FilterEmployeeByScopeAction $filterAction
    ) {}

    public function applyEmployeeScope(Builder $query, Employee $operator): Builder
    {
        return $this->filterAction->execute($query, $operator);
    }
}
