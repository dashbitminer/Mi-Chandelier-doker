<?php

namespace App\Http\Controllers\User\Elearning;

use App\Decorators\Elearning\TopicDecorator;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\Elearning\TopicResource;
use App\Models\Course;
use Carbon\Carbon;
use Inertia\Inertia;
use PDF;

class TopicController extends Controller
{
    public function index($country, $courseID)
    {
        $this->checkPermission('formaciones.cursos');

        $user = auth()->user();

        $course = Course::findOrFail($courseID);

        $userCourse = $user->userCourses()->firstOrCreate(
            ['course_id' => $course->id],
            [
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now(),
                'course_topics_count' => $course->course_topics_count,
            ]
        );

        $course = $userCourse->course;

        $topics = $course->topics()
            ->leftJoin('user_course_topics', function ($join) use ($userCourse) {
                $join->on('course_topics.id', '=', 'user_course_topics.course_topic_id')
                    ->where('user_course_topics.user_course_id', $userCourse->id);
            })
            ->select(
                'course_topics.*',
                'user_course_topics.status as status',
            )
            ->orderBy('course_topics.priority', 'asc')
            ->orderBy('course_topics.name')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/User/Elearning/Topics/index', [
            'userCourse' => $userCourse,
            'course' => $course,
            'topics' => TopicResource::collection($topics),
        ]);
    }

    public function show($country, $courseID, string $id)
    {
        $this->checkPermission('formaciones.cursos');

        $user = auth()->user();

        $course = Course::findOrFail($courseID);
        $topic = $course->topics()->findOrFail($id);
        $topicDecorator = new TopicDecorator($topic);

        $userCourse = $user->userCourses()->where('course_id', $courseID)->first();
        if (! $userCourse) {
            abort(404);
        }

        $userCourseTopic = $userCourse->userCourseTopics()->firstOrNew(['course_topic_id' => $id]);
        if (! $userCourseTopic->exists) {
            $userCourseTopic->note = 0;
            $userCourseTopic->start_date = Carbon::now();
            $userCourseTopic->require_evaluation = $topic->require_evaluation;
            $userCourseTopic->status = 'pending';
        }

        if ($userCourseTopic->evaluation_status != 'approved') {
            $userCourseTopic->status = $topic->require_evaluation ? 'pending' : 'completed';
        }

        $userCourseTopic->save();

        return Inertia::render('Chandelier/User/Elearning/Topics/show', [
            'course' => $course,
            'userCourseTopic' => $userCourseTopic,
            'topic' => $topicDecorator->toArray(),
        ]);
    }

    public function certificate($country, $courseID)
    {
        $this->checkPermission('formaciones.cursos');

        $user = auth()->user();

        $course = $user
            ->courses()
            ->where('user_courses.status', 'completed')
            ->findOrFail($courseID);

        $endDate = Carbon::parse($course->pivot->end_date);
        $endDateFormatted = $endDate->format('d').' días del mes '.$endDate->format('m').' del año '.$endDate->format('Y');

        $pdf = Pdf::loadView('pdf.User.Elearning.certificate', [
            'course' => $course,
            'user' => $user,
            'endDate' => $endDateFormatted,
        ]);

        return $pdf->stream('certificate.pdf');
    }
}
