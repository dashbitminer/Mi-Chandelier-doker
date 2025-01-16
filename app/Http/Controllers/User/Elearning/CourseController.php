<?php

namespace App\Http\Controllers\User\Elearning;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Elearning\CourseResource;
use App\Models\Course;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index($country)
    {
        $this->checkPermission('formaciones.cursos');

        $user = auth()->user();

        $courses = Course::leftJoin('user_courses', function ($join) use ($user) {
            $join->on('courses.id', '=', 'user_courses.course_id')
                ->where('user_courses.user_id', '=', $user->id);
        })
            ->select(
                'courses.*',
                'user_courses.status as status'
            )
            ->where('courses.status', '=', 'published')
            ->whereNot('courses.course_type', '=', 'wizard')
            ->where(function ($query) {
                $query->whereNull('user_courses.status')
                    ->orWhere('user_courses.status', '=', 'pending');
            })
            ->orderBy('name')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/User/Elearning/Courses/index', [
            'user' => $user,
            'courses' => CourseResource::collection($courses),
        ]);
    }

    public function completed($country)
    {
        $this->checkPermission('formaciones.cursos');

        $user = auth()->user();

        $courses = $user->courses()
            ->where('user_courses.status', 'completed')
            ->orderBy('name')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/User/Elearning/Courses/completed', [
            'user' => $user,
            'courses' => CourseResource::collection($courses),
        ]);
    }
}
