<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEvaluationQuestionOption extends Model
{
    use HasFactory;

    public function question()
    {
        return $this->belongsTo(CourseEvaluationQuestion::class);
    }

    public function userCourseEvaluations()
    {
        return $this->hasMany(UserCourseEvaluation::class);
    }
}
