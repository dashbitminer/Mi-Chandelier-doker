<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseTopic extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function booted()
    {
        static::created(function ($courseTopic) {
            $courseTopic->course->increment('course_topics_count');
        });

        static::deleted(function ($courseTopic) {
            $courseTopic->course->increment('course_topics_count');
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function evaluation()
    {
        return $this->hasOne(CourseEvaluation::class);
    }

    public function userCourseTopics()
    {
        return $this->hasOne(UserCourseTopic::class);
    }
}
