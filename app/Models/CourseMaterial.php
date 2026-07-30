<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    protected $fillable = [
        'course_id',
        'material_type',
        'title',
        'content_url_or_path',
        'display_order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
