<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Value;

interface ValueModifierInterface
{
    public function modify($value);
}