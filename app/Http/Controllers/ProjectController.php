<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $projects = Project::all();

        return Inertia::render('Chandelier/Projects/index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Chandelier/Projects/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $country): RedirectResponse
    {
        Project::create($request->all());

        return Redirect::route('projects.index', ['country' => $country]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): Response
    {
        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): Response
    {
        return Inertia::render('Projects/Edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, $country): RedirectResponse
    {
        $project->update($request->all());

        return Redirect::route('projects.index', ['country' => $country]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, $country): RedirectResponse
    {
        $project->delete();

        return Redirect::route('projects.index', ['country' => $country])->with('status', 'Project deleted.');
    }
}
