<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCountry extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
