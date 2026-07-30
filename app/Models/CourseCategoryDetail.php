<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCategoryDetail extends Model
{
    protected $fillable = [
        'category_id',
        'detail_code',
        'detail_name',
        'display_order',
        'icon',
    ];

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_detail_id');
    }
}
