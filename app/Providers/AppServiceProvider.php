<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('at_least_one_selected', function ($attribute, $value, $parameters, $validator) {
            foreach ($value as $item) {
                if ($item['selected']) {
                    return true;
                }
            }
            return false;
        });

		Inertia::share([
	        'flash' => function () {
	            return [
	                'success' => session('success'),
	                'error' => session('error'),
	            ];
	        },
	    ]);
    }
}
