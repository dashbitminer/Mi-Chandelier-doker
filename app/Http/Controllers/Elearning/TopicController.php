<?php

namespace App\Http\Controllers\Elearning;

use App\Decorators\Elearning\CourseEvaluationQuestionDecorator;
use App\Decorators\Elearning\CoursePreviewDecorator;
use App\Decorators\Elearning\TopicDecorator;
use App\Decorators\Elearning\TopicPreviewDecorator;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEvaluation;
use App\Models\CourseEvaluationQuestion;
use App\Models\CourseEvaluationQuestionOption;
use App\Models\CourseTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($country, $courseID)
    {
        $this->checkPermission('admin.formaciones.cursos');

        $user = auth()->user();

        $course = Course::findOrFail($courseID);
        $courseDecorator = new CoursePreviewDecorator($course);

        $topics = $course->topics()
            ->orderBy('priority', 'asc')
            ->orderBy('name')
            ->paginate(config('settings.per_page'));
        $topics->getCollection()->transform(function ($item) {
            $decorator = new TopicPreviewDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/Elearning/Topics/index', [
            'course' => $courseDecorator->toArray(),
            'topics' => $topics,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($country, $courseID)
    {
        $this->checkPermission('admin.formaciones.cursos');

        $user = auth()->user();

        $course = Course::findOrFail($courseID);
        $courseDecorator = new CoursePreviewDecorator($course);

        return Inertia::render('Chandelier/Elearning/Topics/create', [
            'course' => $course,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $country, $courseID)
    {
        $user = auth()->user();

        $course = Course::findOrFail($courseID);

        $validated = $request->validate([
            'data' => 'required|array',
            'data.priority' => 'required|integer',
            'data.name' => 'required|string',
            'data.description' => 'required|string',
            'data.content' => 'nullable|string',
            'data.require_evaluation' => 'required|boolean',
        ]);

        $formData = $validated['data'];
        $topic = new CourseTopic;
        $topic->name = $formData['name'];
        $topic->priority = $formData['priority'];
        $topic->description = $formData['description'];
        $topic->content = $formData['content'];
        $topic->require_evaluation = $formData['require_evaluation'];
        if ($course->topics()->save($topic)) {
            if ($topic->require_evaluation == true) {
                //if(isset($request['data']['evaluation'])){
                $evaluation = new CourseEvaluation;
                $evaluation->course_id = $course->id;
                if ($topic->evaluation()->save($evaluation)) {
                    if (isset($request['data']['questions'])) {
                        foreach ($request['data']['questions'] as $questionData) {
                            $question = new CourseEvaluationQuestion;
                            $question->text = $questionData['text'];
                            $question->priority = $questionData['priority'];

                            if ($evaluation->questions()->save($question)) {
                                if (isset($questionData['options'])) {
                                    foreach ($questionData['options'] as $optionData) {
                                        $option = new CourseEvaluationQuestionOption;
                                        $option->text = $optionData['text'];
                                        $option->priority = $optionData['priority'];
                                        $option->is_correct = $optionData['is_correct'];
                                        $question->options()->save($option);
                                    }
                                }
                            }
                        }
                    }
                }
                //}
            }
        }

        return Redirect::route('elearning.courses.topics.index', ['country' => $country, 'course' => $course->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($country, $courseID, string $id)
    {
        $this->checkPermission('admin.formaciones.cursos');

        $user = auth()->user();

        $course = Course::findOrFail($courseID);
        $courseDecorator = new CoursePreviewDecorator($course);

        $topic = $course->topics()->findOrFail($id);
        $topicDecorator = new TopicDecorator($topic);

        $evaluation = $topic->evaluation;

        $questions = collect([]);
        if ($evaluation) {
            $questions = $evaluation
                ->questions()
                ->orderBy('priority', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $questions = $questions->map(function ($item) {
            $decorator = new CourseEvaluationQuestionDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/Elearning/Topics/edit', [
            'course' => $courseDecorator->toArray(),
            'topic' => $topicDecorator->toArray(),
            'questions' => $questions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $country, $courseID, string $id)
    {
        $user = auth()->user();

        $course = Course::findOrFail($courseID);

        $topic = $course->topics->findOrFail($id);

        $validated = $request->validate([
            'data' => 'required|array',
            'data.name' => 'required|string',
            'data.priority' => 'required|integer',
            'data.description' => 'required|string',
            'data.content' => 'nullable|string',
            'data.require_evaluation' => 'required|boolean',
        ]);

        $formData = $validated['data'];

        $topic->name = $formData['name'];
        $topic->priority = $formData['priority'];
        $topic->description = $formData['description'];
        $topic->content = $formData['content'];
        $topic->require_evaluation = $formData['require_evaluation'];
        if ($topic->save()) {
            if ($topic->require_evaluation) {
                //if(isset($request['data']['evaluation'])){
                $evaluation = $topic->evaluation;
                if (! $evaluation) {
                    $evaluation = new CourseEvaluation;
                    $evaluation->course_id = $course->id;
                    $evaluation->course_topic_id = $topic->id;
                }
                if ($evaluation->save()) {
                    if (isset($request['data']['questions'])) {
                        foreach ($request['data']['questions'] as $questionData) {
                            if ($questionData['is_new']) {
                                $question = new CourseEvaluationQuestion;
                                $question->course_evaluation_id = $evaluation->id;
                            } else {
                                $question = $evaluation->questions->findOrFail($questionData['id']);
                            }

                            $question->text = $questionData['text'];
                            $question->priority = $questionData['priority'];
                            if ($question->save()) {
                                if (isset($questionData['options'])) {
                                    foreach ($questionData['options'] as $optionData) {
                                        if ($optionData['is_new']) {
                                            $option = new CourseEvaluationQuestionOption;
                                            $option->course_evaluation_question_id = $question->id;
                                        } else {
                                            $option = $question->options->findOrFail($optionData['id']);
                                        }

                                        $option->text = $optionData['text'];
                                        $option->priority = $optionData['priority'];
                                        $option->is_correct = $optionData['is_correct'];
                                        $option->save();
                                    }
                                }
                            }
                        }
                    }
                }
                //}
            }
        }

        return Redirect::route('elearning.courses.topics.index', ['country' => $country, 'course' => $course->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($country, $courseID, string $id)
    {
        $this->checkPermission('admin.formaciones.cursos');

        $course = Course::findOrFail($courseID);
        $topic = $course->topics()->findOrFail($id);

        $topic->delete();

        return Redirect::route('elearning.courses.topics.index', ['country' => $country, 'course' => $course->id]);
    }
}
