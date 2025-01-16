<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Resources\Accounting\CountryProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($countryCode)
    {
        $this->checkPermission('contabilidad.proyectos');

        $user = auth()->user();
        $country = $this->country($countryCode);

        $projects = $country->countryProjects()
            ->join('projects', 'country_projects.project_id', '=', 'projects.id')
            ->orderBy('projects.name')
            ->orderBy('country_projects.id')
            ->select('country_projects.*')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/Accounting/Projects/index', [
            'projects' => CountryProjectResource::collection($projects, 'list'),
        ]);
    }

    public function edit($countryCode, string $id)
    {
        $this->checkPermission('contabilidad.proyectos');

        $user = auth()->user();
        $country = $this->country($countryCode);

        $project = $country->countryProjects()->findOrFail($id);

        return Inertia::render('Chandelier/Accounting/Projects/edit', [
            'project' => new CountryProjectResource($project, 'edit'),
        ]);
    }

    public function update(Request $request, $countryCode, string $id)
    {
        $user = auth()->user();
        $country = $this->country($countryCode);

        $project = $country->countryProjects()->findOrFail($id);

        $validated = $request->validate([
            'data' => 'required|array',
            'data.saturday_hours' => 'required|numeric',
            'data.sunday_hours' => 'required|numeric',
        ]);
        $formData = $validated['data'];

        $project->saturday_hours = $formData['saturday_hours'];
        $project->sunday_hours = $formData['sunday_hours'];
        $project->save();

        return Redirect::route('accounting.projects.index', ['country' => $country->alpha_code]);
    }

    public function toggle(Request $request, $countryCode, string $id)
    {
        $this->checkPermission('contabilidad.proyectos');

        $country = $this->country($countryCode);

        $project = $country->countryProjects()->findOrFail($id);

        $project->require_time_sheet = ! $project->require_time_sheet;
        $project->save();

        $resource = new CountryProjectResource($project, 'list');

        return response()->json($resource);
    }
}
