<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEvaluationQuestion extends Model
{
    use HasFactory;

    public function evaluation()
    {
        return $this->belongsTo(CourseEvaluation::class);
    }

    public function options()
    {
        return $this->hasMany(CourseEvaluationQuestionOption::class);
    }

    public function userCourseEvaluations()
    {
        return $this->hasMany(UserCourseEvaluation::class);
    }
}
