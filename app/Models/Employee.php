<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'employee_code',
        'full_name',
        'kana_name',
        'email',
        'phone_number',
        'password',
        'password_error_count',
        'account_status',
        'account_locked_at',
        'is_sys_admin',
        'can_register_employee',
        'can_register_course',
        'can_setting_attendance',
        'authority_effective_range',
        'authority_effective_affiliation_code',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employeeAffiliations()
    {
        return $this->hasMany(EmployeeAffiliation::class);
    }

    public function currentAffiliation()
    {
        return $this->hasOne(EmployeeAffiliation::class)->whereNull('end_date');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }
}
