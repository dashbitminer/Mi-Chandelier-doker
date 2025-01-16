<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEvaluation extends Model
{
    use HasFactory;

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function topic()
    {
        return $this->belongsTo(CourseTopic::class);
    }

    public function questions()
    {
        return $this->hasMany(CourseEvaluationQuestion::class);
    }
}
