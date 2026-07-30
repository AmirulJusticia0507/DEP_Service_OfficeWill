<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'category_detail_id',
        'course_name',
        'description',
        'has_retest',
        'passing_score',
    ];

    public function categoryDetail()
    {
        return $this->belongsTo(CourseCategoryDetail::class, 'category_detail_id');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function todos()
    {
        return $this->hasMany(CourseTodo::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }
}
