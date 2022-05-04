<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

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
        Validator::extend('alpha_spaces_not_html', function ($attribute, $value) {

            if (preg_match('/(?<=<)\w+(?=[^<]*?>)/', $value) || preg_match('/[#$%^&*()+=\-\[\]\'\/{}|":<>?~\\\\]/', $value)) {
                return false;
            } else {
                if (preg_match('/[\p{L} ,.-]+$/u', $value)) {
                    return true;
                } else {
                    return false;
                }
            }

        });
    }
}
