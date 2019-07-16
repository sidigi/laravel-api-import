<?php

declare(strict_types=1);

namespace sidigi\LaravelApiImport\Tests;

use sidigi\LaravelApiImport\LaravelApiImportServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelApiImportServiceProvider::class,
        ];
    }
}
