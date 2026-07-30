<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTodo extends Model
{
    protected $fillable = [
        'course_id',
        'todo_type',
        'title',
        'description',
        'display_order',
        'passing_score',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function responses()
    {
        return $this->hasMany(CourseTodoResponse::class, 'course_todo_id');
    }
}
