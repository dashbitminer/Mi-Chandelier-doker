<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleCountry
{
    protected $defaultCountry = 'SLV';

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        $allowedCountries = $user->countries->pluck('alpha_code')->toArray();

        $country = $request->route('country');

        if ($country === null || trim($country) === '') {
            $defaultCountry = count($allowedCountries) > 0 ? $allowedCountries[0] : $this->defaultCountry;

            return redirect('/'.$defaultCountry);
        }

        if (count($allowedCountries) > 0 && ! in_array($country, $allowedCountries)) {
            return redirect('/'.$allowedCountries[0]);
        }

        return $next($request);
    }
}
