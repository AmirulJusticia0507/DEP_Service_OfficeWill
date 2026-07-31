<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeAffiliation extends Model
{
    protected $fillable = [
        'company_id',
        'employee_id',
        'affiliation_code',
        'job_id',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function scopeOverlapping(Builder $query, string $startDate, ?string $endDate): Builder
    {
        return $query->where(function (Builder $q) use ($startDate, $endDate) {
            if ($endDate !== null) {
                $q->where('start_date', '<=', $endDate);
            }

            $q->where(function (Builder $q2) use ($startDate) {
                $q2->whereNull('end_date')
                    ->orWhere('end_date', '>=', $startDate);
            });
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class, 'affiliation_code', 'affiliation_code');
    }

    public function job()
    {
        return $this->belongsTo(MasterJob::class, 'job_id', 'job_id');
    }
}
