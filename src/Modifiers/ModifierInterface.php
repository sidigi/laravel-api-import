<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers;

interface ModifierInterface
{
    public function get(): array;
}