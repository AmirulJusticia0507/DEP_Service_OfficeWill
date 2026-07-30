<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    protected $fillable = [
        'category_code',
        'category_name',
        'display_order',
        'icon',
    ];

    public function details()
    {
        return $this->hasMany(CourseCategoryDetail::class, 'category_id');
    }
}
