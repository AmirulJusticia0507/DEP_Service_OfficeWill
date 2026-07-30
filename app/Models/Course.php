<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    protected $fillable = [
        'category_detail_id',
        'course_name',
        'description',
        'photo',
        'has_retest',
        'passing_score',
    ];

    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->photo ? Storage::url($this->photo) : null);
    }

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
