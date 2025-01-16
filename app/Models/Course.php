<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function courseCountries()
    {
        return $this->hasMany(CourseCountry::class);
    }

    public function courseCountryProjects()
    {
        return $this->hasMany(CourseCountryProject::class);
    }

    public function courseEvaluation()
    {
        return $this->hasOne(CourseEvaluation::class);
    }

    public function topics()
    {
        return $this->hasMany(CourseTopic::class);
    }

    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
