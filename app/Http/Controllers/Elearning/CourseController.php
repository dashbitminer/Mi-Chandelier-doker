<?php

namespace App\Http\Controllers\Elearning;

use App\Decorators\Elearning\CountryDecorator;
use App\Decorators\Elearning\CountryProjectDecorator;
use App\Decorators\Elearning\CourseCountryDecorator;
use App\Decorators\Elearning\CourseCountryProjectDecorator;
use App\Decorators\Elearning\CourseDecorator;
use App\Decorators\Elearning\CoursePreviewDecorator;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Country;
use App\Models\CountryProject;
use App\Models\Course;
use App\Models\CourseCountry;
use App\Models\CourseCountryProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('admin.formaciones.cursos');

        $user = auth()->user();

        $courses = Course::query()
            ->orderBy('name')
            ->paginate(config('settings.per_page'));

        $courses->getCollection()->transform(function ($item) {
            $decorator = new CoursePreviewDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/Elearning/Courses/index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('admin.formaciones.cursos');

        $user = auth()->user();

        $countries = Country::query()
            ->orderBy('name')
            ->paginate(config('settings.per_page'));
        $countries->getCollection()->transform(function ($item) {
            $decorator = new CountryDecorator($item);

            return $decorator->toArray();
        });

        $countryProjects = CountryProject::query()
            ->join('projects', 'country_projects.project_id', '=', 'projects.id')
            ->join('countries', 'country_projects.country_id', '=', 'countries.id')
            ->orderBy('projects.name')
            ->orderBy('countries.name')
            ->select('country_projects.*')
            ->paginate(config('settings.per_page'));
        $countryProjects->getCollection()->transform(function ($item) {
            $decorator = new CountryProjectDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/Elearning/Courses/create', [
            'categories' => $this->categories(),
            'countries' => $countries,
            'countryProjects' => $countryProjects,
            'courseTypes' => [
                ['id' => 'required', 'name' => 'Curso obligatorio'],
                ['id' => 'optional', 'name' => 'Curso opcional'],
                ['id' => 'wizard', 'name' => 'Ayuda'],
            ],
            'statuses' => [
                ['id' => 'unpublished', 'name' => 'Sin publicar'],
                ['id' => 'published', 'name' => 'Publicado'],
            ],
            'scopes' => [
                ['id' => 'country', 'name' => 'Nacional'],
                ['id' => 'project', 'name' => 'Proyectos'],
                ['id' => 'all', 'name' => 'Regional'],
            ],

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $countryISO)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'data' => 'required|array',
            'data.name' => 'required|string',
            'data.category_id' => 'required|exists:categories,id',
            'data.description' => 'required|string',
            'data.scope' => 'required|string',
            'data.course_type' => 'required|string',
            'data.country_project_ids' => 'nullable|array',
            'data.country_ids' => 'nullable|array',
        ]);
        $formData = $validated['data'];
        $course = new Course;
        $course->name = $formData['name'];
        $course->description = $formData['description'];
        $course->category_id = $formData['category_id'];
        $course->scope = $formData['scope'];
        $course->course_type = $formData['course_type'];
        $course->user_id = $user->id;
        $course->status = 'unpublished';
        if ($course->save()) {
            if (isset($formData['country_ids'])) {
                foreach ($formData['country_ids'] as $country_id) {
                    $country = Country::find($country_id);
                    if (! $country) {
                        continue;
                    }

                    $courseCountry = new CourseCountry;
                    $courseCountry->country_id = $country->id;
                    $course->courseCountries()->save($courseCountry);
                }
            }

            if (isset($formData['country_project_ids'])) {
                foreach ($formData['country_project_ids'] as $country_project_id) {
                    $countryProject = CountryProject::find($country_project_id);
                    if (! $countryProject) {
                        continue;
                    }

                    $courseCountryProject = new CourseCountryProject;
                    $courseCountryProject->country_project_id = $countryProject->id;
                    $courseCountryProject->project_id = $countryProject->project_id;
                    $courseCountryProject->country_id = $countryProject->country_id;
                    $course->courseCountryProjects()->save($courseCountryProject);
                }
            }
        }

        return Redirect::route('elearning.courses.index', ['country' => $countryISO]);
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
    public function edit($country, string $id)
    {
        $this->checkPermission('admin.formaciones.cursos');

        $course = Course::findOrFail($id);
        $courseDecorator = new CourseDecorator($course);

        $countries = Country::query()
            ->orderBy('name')
            ->paginate(config('settings.per_page'));
        $countries->getCollection()->transform(function ($item) {
            $decorator = new CountryDecorator($item);

            return $decorator->toArray();
        });

        $countryProjects = CountryProject::query()
            ->join('projects', 'country_projects.project_id', '=', 'projects.id')
            ->join('countries', 'country_projects.country_id', '=', 'countries.id')
            ->orderBy('projects.name')
            ->orderBy('countries.name')
            ->select('country_projects.*')
            ->paginate(config('settings.per_page'));
        $countryProjects->getCollection()->transform(function ($item) {
            $decorator = new CountryProjectDecorator($item);

            return $decorator->toArray();
        });

        $selectedCountries = collect($course->courseCountries()->get());
        $selectedCountries->transform(function ($item) {
            $decorator = new CourseCountryDecorator($item);

            return $decorator->toArray();
        });

        $selectedCountryProjects = collect($course->courseCountryProjects()->get());
        $selectedCountryProjects->transform(function ($item) {
            $decorator = new CourseCountryProjectDecorator($item);

            return $decorator->toArray();
        });

        return Inertia::render('Chandelier/Elearning/Courses/edit', [
            'course' => $courseDecorator->toArray(),
            'categories' => $this->categories(),
            'countries' => $countries,
            'countryProjects' => $countryProjects,
            'selectedCountries' => $selectedCountries,
            'selectedCountryProjects' => $selectedCountryProjects,
            'courseTypes' => [
                ['id' => 'required', 'name' => 'Curso obligatorio'],
                ['id' => 'optional', 'name' => 'Curso opcional'],
                ['id' => 'wizard', 'name' => 'Ayuda'],
            ],
            'statuses' => [
                ['id' => 'unpublished', 'name' => 'Sin publicar'],
                ['id' => 'published', 'name' => 'Publicado'],
            ],
            'scopes' => [
                ['id' => 'country', 'name' => 'Nacional'],
                ['id' => 'project', 'name' => 'Proyectos'],
                ['id' => 'all', 'name' => 'Regional'],
            ],

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $countryISO, string $id)
    {
        $user = auth()->user();

        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'data' => 'required|array',
            'data.name' => 'required|string',
            'data.category_id' => 'required|exists:categories,id',
            'data.description' => 'required|string',
            'data.scope' => 'required|string',
            'data.course_type' => 'required|string',
            'data.country_project_ids' => 'nullable|array',
            'data.country_ids' => 'nullable|array',
        ]);
        $formData = $validated['data'];

        $course->name = $formData['name'];
        $course->category_id = $formData['category_id'];
        $course->description = $formData['description'];
        $course->scope = $formData['scope'];
        $course->course_type = $formData['course_type'];
        $course->user_id = $user->id;
        $course->status = 'unpublished';
        if ($course->save()) {
            if (isset($formData['country_ids'])) {
                foreach ($formData['country_ids'] as $country_id) {
                    $country = Country::find($country_id);
                    if (! $country) {
                        continue;
                    }

                    $courseCountry = $course->courseCountries()->firstOrCreate(
                        [
                            'country_id' => $country->id,
                        ]
                    );
                }
            }

            $course->courseCountries()->whereNotIn('country_id', $formData['country_ids'])->delete();

            if (isset($formData['country_project_ids'])) {
                foreach ($formData['country_project_ids'] as $country_project_id) {
                    $countryProject = CountryProject::find($country_project_id);
                    if (! $countryProject) {
                        continue;
                    }

                    $courseCountryProject = $course->courseCountryProjects()->firstOrCreate(
                        [
                            'country_project_id' => $countryProject->id,
                            'country_id' => $countryProject->country_id,
                            'project_id' => $countryProject->project_id,
                        ]
                    );
                }
            }

            $course->courseCountryProjects()->whereNotIn('country_project_id', $formData['country_project_ids'])->delete();
        }

        return Redirect::route('elearning.courses.index', ['country' => $countryISO]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($country, string $id)
    {
        $this->checkPermission('admin.formaciones.cursos');

        $course = Course::findOrFail($id);

        $course->delete();

        return Redirect::route('elearning.courses.index', ['country' => $country]);
    }

    public function toggle($country, string $id)
    {
        $this->checkPermission('admin.formaciones.cursos');

        $course = Course::findOrFail($id);

        $course->status = $course->status == 'unpublished' ? 'published' : 'unpublished';
        $course->save();

        $courseDecorator = new CoursePreviewDecorator($course);

        return response()->json($courseDecorator->toArray());
    }

    private function categories()
    {
        return Category::where('active', true)->orderBy('name')->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        });
    }
}
