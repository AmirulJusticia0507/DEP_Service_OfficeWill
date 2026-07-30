<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterJob extends Model
{
    protected $table = 'master_jobs';

    protected $fillable = [
        'company_id',
        'job_id',
        'job_title',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
