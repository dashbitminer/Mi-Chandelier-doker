<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {

            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find or create a user
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user && $user->active == true) {
                Auth::login($user, true);

                return redirect('/'); // Redirect to a desired route
            } elseif ($user && $user->active != true) {
                return redirect()->route('inactive');
            } else {
                return redirect()->route('unassigned');
            }
        } catch (\Exception $exception) {
            return redirect()->route('login')->withErrors('Authentication failed');
        }
    }
}
