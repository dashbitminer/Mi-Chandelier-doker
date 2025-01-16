<?php

namespace App\Observers;

use App\Models\UserCourseTopic;

class UserCourseTopicObserver
{
    public function created(UserCourseTopic $userCourseTopic): void
    {
        if ($userCourseTopic->getOriginal('status') !== 'completed' && $userCourseTopic->status === 'completed') {
            $course = $userCourseTopic->course;

            if ($course->course_topics_count === $course->userCourseTopics->where('status', 'completed')->count()) {
                $course->update(['status' => 'completed']);
            }
        }
    }

    public function updated(UserCourseTopic $userCourseTopic): void
    {
        if ($userCourseTopic->getOriginal('status') !== 'completed' && $userCourseTopic->status === 'completed') {
            $course = $userCourseTopic->course;

            if ($course->course_topics_count === $course->userCourseTopics->where('status', 'completed')->count()) {
                $course->update(['status' => 'completed']);
            }
        }
    }
}
