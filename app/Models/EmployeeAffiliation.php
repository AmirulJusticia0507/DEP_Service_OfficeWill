<?php

namespace App\Models;

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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class, 'affiliation_code', 'affiliation_code');
    }
}
