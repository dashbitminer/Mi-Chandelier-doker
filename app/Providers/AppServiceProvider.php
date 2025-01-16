<?php

namespace App\Providers;

use App\Models\Country;
use App\Models\UserCourseTopic;
use App\Observers\UserCourseTopicObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        UserCourseTopic::observe(UserCourseTopicObserver::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }
        Inertia::share([
            'auth' => function () {
                return [
                    'user' => Auth::user() ? Auth::user()->only('name') : null,
                ];
            },
            'permissions' => function () {
                $user = Auth::user();

                return $user ? $user->getAllPermissions()->pluck('name') : [];
            },
            'currentCountry' => function () {
                return $this->app['request']->route('country');
            },
            'brilo' => function () {
                $user = Auth::user();
                $country = Country::where('alpha_code', $this->app['request']->route('country'))->first();

                $config = [
                    'url' => null,
                    'token' => null,
                ];

                if (! $country || ! $country->brilo_url) {
                    return $config;
                }

                $userToken = $user->userTokens->where('country_id', $country->id)->first();

                if (! $userToken || ! $userToken->token) {
                    return $config;
                }

                $config['url'] = $country->brilo_url;
                $config['token'] = $userToken->token;

                return $config;
            },
        ]);
    }
}
