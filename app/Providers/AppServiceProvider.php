<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerRepositories();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');
        Passport::tokensExpireIn(now()->addYear(20));
        Passport::tokensCan([
			'client' => 'client',
		]);
        
        Validator::extend('without_spaces', function ($attribute, $value, $parameters, $validator) {
            return !preg_match('/\s/', $value);
        });

        Validator::replacer('without_spaces', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute field should not contain spaces.');
        });
    }

    private function registerRepositories()
    {
        $files = scandir(app_path('Repositories/Interfaces'));
        foreach ($files as $file) {
            if (!Str::endsWith($file, '.php')) {
                continue;
            }
            $file = rtrim($file, '.php');
            $this->app->bind(
                "App\\Repositories\\Interfaces\\{$file}",
                "App\\Repositories\\Eloquent\\{$file}Eloquent"
            );
        }
    }
}
