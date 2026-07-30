<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'employee_id',
        'enrollment_deadline',
        'status',
    ];

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
