<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Value;

class DummyModifier implements ValueModifierInterface
{
    public function modify($value)
    {
        return $value;
    }
}