<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'enrollment_id', 'course_todo_id', 'attempt_number',
        'total_score', 'max_score', 'status', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'integer',
            'max_score' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function enrollment()
    {
        return $this->belongsTo(CourseEnrollment::class);
    }

    public function courseTodo()
    {
        return $this->belongsTo(CourseTodo::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }
}
