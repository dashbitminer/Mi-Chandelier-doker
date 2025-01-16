<?php

namespace App\Http\Controllers;

use App\Models\Country;

abstract class Controller
{
    protected $country;

    protected function country($code)
    {
        return Country::where('alpha_code', $code)->first();
    }

    protected function checkPermission(string $permission): void
    {
        $user = auth()->user();

        if (! $user || ! $user->hasPermissionTo($permission)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }
}
