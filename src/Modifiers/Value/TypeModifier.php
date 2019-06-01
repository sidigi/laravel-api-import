<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Value;

abstract class TypeModifier implements ValueModifierInterface
{
    protected $types = [];

    public function modify($value)
    {
        foreach ($this->types as $type){
            if (isset($this->types[$value])){
                return $type;
            }
        }

        return $value;
    }
}