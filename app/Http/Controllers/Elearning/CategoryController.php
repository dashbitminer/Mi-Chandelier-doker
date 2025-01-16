<?php

namespace App\Http\Controllers\Elearning;

use App\Http\Controllers\Controller;
use App\Http\Resources\Elearning\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('admin.formaciones.categorias');

        $user = auth()->user();

        $categories = Category::query()
            ->orderBy('name')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/Elearning/Categories/index', [
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('admin.formaciones.categorias');

        $user = auth()->user();

        return Inertia::render('Chandelier/Elearning/Categories/create', []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $country)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'data' => 'required|array',
            'data.name' => 'required|string',
            'data.description' => 'nullable|string',
        ]);
        $formData = $validated['data'];
        $category = new Category;
        $category->name = $formData['name'];
        $category->description = $formData['description'];
        $category->save();

        return Redirect::route('elearning.categories.index', ['country' => $country]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($country, string $id)
    {
        $this->checkPermission('admin.formaciones.categorias');

        $user = auth()->user();

        $category = Category::findOrFail($id);

        return Inertia::render('Chandelier/Elearning/Categories/edit', [
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $country, string $id)
    {
        $user = auth()->user();

        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'data' => 'required|array',
            'data.name' => 'required|string',
            'data.description' => 'nullable|string',
        ]);
        $formData = $validated['data'];

        $category->name = $formData['name'];
        $category->description = $formData['description'];
        $category->save();

        return Redirect::route('elearning.categories.index', ['country' => $country]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($country, string $id)
    {
        $this->checkPermission('admin.formaciones.categorias');

        $category = Category::findOrFail($id);

        $category->delete();

        return Redirect::route('elearning.categories.index', ['country' => $country]);
    }

    public function toggle(Request $request, $country, string $id)
    {

        $this->checkPermission('admin.formaciones.categorias');

        $category = Category::findOrFail($id);

        $category->active = ! $category->active;
        $category->save();

        $resource = new CategoryResource($category);

        return response()->json($resource);
    }
}
