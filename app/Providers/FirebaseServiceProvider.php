<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

use Firebase\Auth\Token\Verifier;
use App\Firebase\Guard as FirebaseGuard;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }


    public function register()
    {
        // $this->app->singleton(Verifier::class, function ($app) {
        //     return new Verifier(config('services.firebase.project_name'));
        // });

        // Auth::viaRequest('firebase', function ($request) {
        //     return app(FirebaseGuard::class)->public_user($request);
        // });
    }
}
