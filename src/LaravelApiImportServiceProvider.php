<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use Illuminate\Support\ServiceProvider;

class LaravelApiImportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()){
            $this->registerPublishing();
        }
    }

    public function register(): void
    {

    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-api-import.php' => config_path('laravel-api-import.php')
        ], 'laravel-api-import-config');
    }
}