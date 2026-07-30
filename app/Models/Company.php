<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'login_url',
        'icon_storage_path',
        'material_storage_path',
    ];

    public function affiliations()
    {
        return $this->hasMany(Affiliation::class);
    }

    public function masterJobs()
    {
        return $this->hasMany(MasterJob::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
