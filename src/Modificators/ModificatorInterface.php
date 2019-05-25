<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modificators;

interface ModificatorInterface
{
    public function get(): array;
}