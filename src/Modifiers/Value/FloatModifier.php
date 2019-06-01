<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Value;

class FloatModifier implements ValueModifierInterface
{
    public function modify($value)
    {
        if ((string)(float)$value === $value) {
            if (strpos($value, '.') !== false) {
                return (float) $value;
            }

            return (int) $value;
        }

        return $value;
    }
}