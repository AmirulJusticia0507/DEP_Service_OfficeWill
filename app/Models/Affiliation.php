<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliation extends Model
{
    protected $fillable = [
        'company_id',
        'affiliation_code',
        'affiliation_name',
        'display_order',
        'organization_type',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employeeAffiliations()
    {
        return $this->hasMany(EmployeeAffiliation::class, 'affiliation_code', 'affiliation_code');
    }
}
