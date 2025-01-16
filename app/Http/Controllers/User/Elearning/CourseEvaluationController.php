<?php

namespace App\Http\Controllers\User\Elearning;

use App\Decorators\User\Elearning\CourseEvaluationQuestionDecorator;
use App\Decorators\User\Elearning\CoursePreviewDecorator;
use App\Decorators\User\Elearning\CourseTopicPreviewDecorator;
use App\Decorators\User\Elearning\UserCourseTopicDecorator;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserCourseEvaluation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseEvaluationController extends Controller
{
    public function store($country, $courseID, $topicID)
    {
        $user = auth()->user();

        $course = Course::findOrFail($courseID);
        $courseDecorator = new CoursePreviewDecorator($course);

        $topic = $course->topics()->findOrFail($topicID);
        $topicDecorator = new CourseTopicPreviewDecorator($topic);

        $userCourse = $user->userCourses()->where('course_id', $course->id)->first();
        if (! $userCourse) {
            abort(404);
        }

        $userCourseTopic = $userCourse
            ->userCourseTopics()
            ->where('course_topic_id', $topic->id)
            ->whereIn('evaluation_status', ['pending', 'failed'])
            ->first();
        if (! $userCourseTopic) {
            abort(404);
        }

        $userCourseTopic->note = 0;
        $userCourseTopic->evaluation_status = 'pending';
        $userCourseTopic->evaluation_date = Carbon::now();
        $userCourseTopic->save();

        $userCourseTopic->userCourseEvaluations()->update(['is_correct' => false]);

        $questions = $topic
            ->evaluation
            ->questions()
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
        $questions = $questions->map(function ($item) {
            $decorator = new CourseEvaluationQuestionDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/User/Elearning/CourseEvaluations/create', [
            'course' => $courseDecorator->toArray(),
            'topic' => $topicDecorator->toArray(),
            'questions' => $questions,
        ]);
    }

    public function create(Request $request, $country, $courseID, $topicID)
    {
        $this->checkPermission('formaciones.cursos');

        $user = auth()->user();

        $course = Course::findOrFail($courseID);
        $courseDecorator = new CoursePreviewDecorator($course);

        $topic = $course->topics()->findOrFail($topicID);
        $topicDecorator = new CourseTopicPreviewDecorator($topic);

        $questions = $topic
            ->evaluation
            ->questions()
            ->orderBy('created_at', 'asc')
            ->get();

        $userCourse = $user->userCourses()->where('course_id', $course->id)->first();
        if (! $userCourse) {
            abort(404);
        }

        $userCourseTopic = $userCourse
            ->userCourseTopics()
            ->where('course_topic_id', $topic->id)
            ->whereIn('evaluation_status', ['pending', 'failed'])
            ->first();
        if (! $userCourseTopic) {
            abort(404);
        }

        $data = $request['data']['questions'];
        $totalCorrentQuestions = 0;
        foreach ($questions as $question) {
            $value = $data[strval($question->id)];
            if ($value === null || $value === '') {
                continue;
            }

            $option = $question->options->where('id', $value)->first();
            if (! $option) {
                continue;
            }

            $userCourseEvaluation = $userCourseTopic->userCourseEvaluations()->where('course_evaluation_question_id', $question->id)->first();
            if (! $userCourseEvaluation) {
                $userCourseEvaluation = new UserCourseEvaluation;
                $userCourseEvaluation->user_course_topic_id = $userCourseTopic->id;
                $userCourseEvaluation->course_evaluation_question_id = $question->id;
            }

            $userCourseEvaluation->course_evaluation_question_option_id = $value;
            $userCourseEvaluation->is_correct = $option->is_correct;
            if (! $userCourseEvaluation->save()) {
                continue;
            }

            if ($userCourseEvaluation->is_correct) {
                $totalCorrentQuestions += 1;
            }
        }

        $totalQuestions = count($questions);
        $note = 10 * $totalCorrentQuestions / $totalQuestions;

        $userCourseTopic->note = round($note, 2);
        $userCourseTopic->evaluation_status = 'failed';
        if ($userCourseTopic->note >= 7) {
            $userCourseTopic->evaluation_status = 'approved';
            $userCourseTopic->status = 'completed';
        }
        $userCourseTopic->save();

        $userCourseTopicDecorator = new UserCourseTopicDecorator($userCourseTopic);

        return Inertia::render('Chandelier/User/Elearning/CourseEvaluations/show', [
            'course' => $courseDecorator->toArray(),
            'topic' => $topicDecorator->toArray(),
            'userCourseTopic' => $userCourseTopicDecorator->toArray(),
        ]);
    }
}
