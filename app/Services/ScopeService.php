<?php

namespace App\Services;

use App\Models\Affiliation;
use App\Models\CourseEnrollment;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;

class ScopeService
{
    public function scopeEmployeeQuery(Builder $query, Employee $operator): Builder
    {
        if ($this->isUnrestricted($operator)) {
            return $query;
        }

        $codes = $this->authorizedAffiliationCodes($operator);

        return $query->whereHas('currentAffiliation', function ($q) use ($codes) {
            $q->whereIn('affiliation_code', $codes);
        });
    }

    public function scopeEnrollmentQuery(Builder $query, Employee $operator): Builder
    {
        return $query->whereHas('employee', function ($q) use ($operator) {
            $q->where('company_id', $operator->company_id);

            if (! $this->isUnrestricted($operator)) {
                $codes = $this->authorizedAffiliationCodes($operator);
                $q->whereHas('currentAffiliation', function ($a) use ($codes) {
                    $a->whereIn('affiliation_code', $codes);
                });
            }
        });
    }

    public function canAccessEmployee(Employee $operator, Employee $target): bool
    {
        if ($target->company_id !== $operator->company_id) {
            return false;
        }

        if ($this->isUnrestricted($operator)) {
            return true;
        }

        return $this->canAccessAffiliation($operator, $target->currentAffiliation?->affiliation_code);
    }

    public function canAccessEnrollment(Employee $operator, CourseEnrollment $enrollment): bool
    {
        $target = $enrollment->employee;

        return $target !== null && $this->canAccessEmployee($operator, $target);
    }

    public function canAccessAffiliation(Employee $operator, ?string $affiliationCode): bool
    {
        if ($this->isUnrestricted($operator)) {
            return true;
        }

        if (empty($affiliationCode)) {
            return false;
        }

        return in_array($affiliationCode, $this->authorizedAffiliationCodes($operator), true);
    }

    public function authorizedAffiliationCodes(Employee $operator): array
    {
        if ($this->isUnrestricted($operator)) {
            return Affiliation::where('company_id', $operator->company_id)->pluck('affiliation_code')->all();
        }

        $code = $operator->authority_effective_affiliation_code;

        if (empty($code)) {
            return [];
        }

        if ($operator->authority_effective_range === 'ONLY') {
            return [$code];
        }

        return Affiliation::where('company_id', $operator->company_id)
            ->where('affiliation_code', 'LIKE', $code.'%')
            ->pluck('affiliation_code')
            ->all();
    }

    private function isUnrestricted(Employee $operator): bool
    {
        return $operator->isAdmin() || $operator->authority_effective_range === 'ALL';
    }
}
