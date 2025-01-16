<?php

namespace App\Http\Controllers\User\Elearning;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Elearning\CourseResource;
use App\Models\Course;
use Inertia\Inertia;

class HelpCenterController extends Controller
{
    public function index()
    {
        $this->checkPermission('formaciones.centro-ayuda');

        $user = auth()->user();

        $courses = Course::query()
            ->where('courses.status', '=', 'published')
            ->where('courses.course_type', '=', 'wizard')
            ->orderBy('name')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/User/Elearning/HelpCenter/index', [
            'user' => $user,
            'courses' => CourseResource::collection($courses),
        ]);
    }
}
