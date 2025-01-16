<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCountryProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_project_id',
        'project_id',
        'country_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function countryProject()
    {
        return $this->belongsTo(CountryProject::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
