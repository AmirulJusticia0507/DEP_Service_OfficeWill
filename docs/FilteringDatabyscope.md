namespace App\Services;

use App\Models\Employee;

class ScopeFilterService
{
    public static function applyEmployeeScope($query, Employee $operator)
    {
        $scopeRange = $operator->authority_effective_range; // 'ONLY', 'BELOW', 'ALL'
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
            // Mengambil cabang ini dan sub-cabang di bawahnya (prefix matching / hirarki code)
            return $query->whereHas('currentAffiliation', function ($q) use ($operatorAffCode) {
                $q->where('affiliation_code', 'LIKE', $operatorAffCode . '%');
            });
        }

        return $query;
    }
}