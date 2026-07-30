<?php

namespace App\Actions\Employee;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;

class FilterEmployeeByScopeAction
{
    public function execute(Builder $query, Employee $operator): Builder
    {
        $scopeRange = $operator->authority_effective_range;
        $operatorAffCode = $operator->authority_effective_affiliation_code;

        if ($scopeRange === 'ALL') {
            return $query;
        }

        if ($scopeRange === 'ONLY') {
            return $query->whereHas('currentAffiliation', function ($q) use ($operatorAffCode) {
                $q->where('affiliation_code', $operatorAffCode);
            });
        }

        if ($scopeRange === 'BELOW') {
            return $query->whereHas('currentAffiliation', function ($q) use ($operatorAffCode) {
                $q->where('affiliation_code', 'LIKE', $operatorAffCode . '%');
            });
        }

        return $query;
    }
}
