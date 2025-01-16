<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_evaluation_question_option_id',
        'course_evaluation_question_id',
        'user_course_topic_id',
        'is_correct',
        'created_at',
    ];

    public function question()
    {
        return $this->belongsTo(CourseEvaluationQuestion::class, 'course_evaluation_question_id');
    }

    public function option()
    {
        return $this->belongsTo(CourseEvaluationQuestionOption::class, 'course_evaluation_question_option_id');
    }
}
