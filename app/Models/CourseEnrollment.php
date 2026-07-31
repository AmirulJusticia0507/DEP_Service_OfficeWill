<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'employee_id',
        'enrollment_deadline',
        'status',
    ];

    public function submissionLockReason(): ?string
    {
        if ($this->status !== 'ENROLLED') {
            return 'Kursus sudah tidak aktif (selesai atau dibatalkan).';
        }

        if ($this->enrollment_deadline !== null
            && Carbon::parse($this->enrollment_deadline)->lt(Carbon::today())) {
            return 'Deadline kursus telah lewat.';
        }

        return null;
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function todoResponses()
    {
        return $this->hasMany(CourseTodoResponse::class, 'enrollment_id');
    }
}
