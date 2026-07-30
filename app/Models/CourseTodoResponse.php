<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTodoResponse extends Model
{
    protected $fillable = [
        'enrollment_id',
        'course_todo_id',
        'response_content',
        'score',
        'status',
    ];

    public function enrollment()
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    public function courseTodo()
    {
        return $this->belongsTo(CourseTodo::class, 'course_todo_id');
    }
}
