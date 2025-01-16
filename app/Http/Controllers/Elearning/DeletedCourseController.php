<?php

namespace App\Http\Controllers\Elearning;

use App\Decorators\Elearning\CoursePreviewDecorator;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DeletedCourseController extends Controller
{
    public function index()
    {
        $this->checkPermission('admin.formaciones.cursos-eliminados');

        $user = auth()->user();

        $courses = Course::onlyTrashed()
            ->orderBy('name')
            ->paginate(config('settings.per_page'));

        $courses->getCollection()->transform(function ($item) {
            $decorator = new CoursePreviewDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/Elearning/DeletedCourses/index', [
            'courses' => $courses,
        ]);
    }

    public function restore(Request $request, $country, string $id)
    {
        $this->checkPermission('admin.formaciones.cursos-eliminados');

        $course = Course::onlyTrashed()->findOrFail($id);

        $course->restore();

        return Redirect::route('elearning.deleted-courses.index', ['country' => $country]);
    }
}
