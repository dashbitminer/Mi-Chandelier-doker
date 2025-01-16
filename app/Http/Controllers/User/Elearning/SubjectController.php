<?php

namespace App\Http\Controllers\User\Elearning;

use App\Decorators\Elearning\TopicDecorator;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\Elearning\TopicResource;
use App\Models\Course;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function index($helpCenterID)
    {
        $this->checkPermission('formaciones.cursos');

        $user = auth()->user();

        $course = Course::findOrFail($helpCenterID);

        $topics = $course->topics()
            ->orderBy('course_topics.name')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/User/Elearning/Subjects/index', [
            'course' => $course,
            'topics' => TopicResource::collection($topics),
        ]);
    }

    public function show($helpCenterID, string $id)
    {
        $this->checkPermission('formaciones.cursos');

        $user = auth()->user();

        $course = Course::findOrFail($helpCenterID);
        $topic = $course->topics()->findOrFail($id);
        $topicDecorator = new TopicDecorator($topic);

        return Inertia::render('Chandelier/User/Elearning/Subjects/show', [
            'course' => $course,
            'topic' => $topicDecorator->toArray(),
        ]);
    }
}
