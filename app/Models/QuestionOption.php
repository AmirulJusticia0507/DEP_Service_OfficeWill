<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $fillable = [
        'question_id', 'option_text', 'is_correct', 'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
