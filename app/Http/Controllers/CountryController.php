<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $countries = $user->countryProjects->mapWithKeys(function ($countryProject) {
            $country = $countryProject->country;

            return [$country->alpha_code => $country->label];
        })->unique()->toArray();

        return response()->json($countries);
    }
}
