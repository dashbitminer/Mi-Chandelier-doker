<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class UserSupportController extends Controller
{
    public function unassigned()
    {
        $user = auth()->user();

        return Inertia::render('Chandelier/UserSupports/unassigned', [
        ]);
    }

    public function inactive()
    {
        $user = auth()->user();

        return Inertia::render('Chandelier/UserSupports/inactive', [
        ]);
    }
}
