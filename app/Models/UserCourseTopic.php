<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_topic_id',
        'user_course_id',
        'status',
        'note',
        'require_validation',
        'start_date',
        'evaluation_date',
        'created_at',
    ];

    public function topic()
    {
        return $this->belongsTo(CourseTopic::class, 'course_topic_id');
    }

    public function course()
    {
        return $this->belongsTo(UserCourse::class, 'user_course_id');
    }

    public function userCourseEvaluations()
    {
        return $this->hasMany(UserCourseEvaluation::class);
    }
}
