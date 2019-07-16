<?php

declare(strict_types=1);

namespace sidigi\LaravelApiImport\Formatters;

interface ModificatorInterface
{
    public function get(): array;
}
