<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use Illuminate\Support\ServiceProvider;

class ApiImportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()){
            $this->registerPublishing();
        }

        $this->registerModifiers();
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-api-import.php' => config_path('laravel-api-import.php')
        ], 'laravel-api-import-config');
    }

    private function registerModifiers(): void
    {
        Modifier::defaultModifiers([
            \sidigi\LaravelApiImport\Modifiers\Arr\FieldsArrModifier::class,
            \sidigi\LaravelApiImport\Modifiers\Arr\TypeConversationArrModifier::class,
        ]);
    }
}