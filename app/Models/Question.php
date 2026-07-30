<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'course_id', 'question_type', 'question_text', 'points', 'display_order',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'display_order' => 'integer',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('display_order');
    }

    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }
}
