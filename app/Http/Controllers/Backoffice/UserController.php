<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Resources\Backoffice\RoleResource as RoleResource;
use App\Http\Resources\Backoffice\UserResource as UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('admin.usuarios');

        $user = auth()->user();

        $users = User::query()
            ->orderBy('name')
            ->paginate(config('settings.per_page'));

        return Inertia::render('Chandelier/Backoffice/Users/index', [
            'users' => UserResource::collection($users),
        ]);
    }

    public function edit($country, string $id)
    {
        $this->checkPermission('admin.usuarios');

        $user = auth()->user();

        $resource = User::findOrFail($id);

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Chandelier/Backoffice/Users/edit', [
            'user' => new UserResource($resource),
            'roles' => RoleResource::collection($roles),
        ]);
    }

    public function update(Request $request, $country, string $id)
    {
        $user = auth()->user();

        $resource = User::findOrFail($id);

        $validated = $request->validate([
            'data' => 'required|array',
            'data.role' => 'required|string',
        ]);
        $formData = $validated['data'];

        $resource->syncRoles($formData['role']);

        return Redirect::route('backoffice.users.index', ['country' => $country]);
    }

    public function toggle(Request $request, $country, string $id)
    {

        $this->checkPermission('admin.usuarios');

        $resource = User::findOrFail($id);

        $resource->active = ! $resource->active;
        $resource->save();

        $userResource = new UserResource($resource);

        return response()->json($userResource);
    }
}
