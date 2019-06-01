<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Arr;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use sidigi\LaravelApiImport\Modifiers\Value\DummyModifier;

abstract class FieldConversionArrModifier implements ArrModifierInterface
{
    public function modify(array $data): array
    {
        $data = Arr::dot($data);

        $array = [];
        foreach ($data as $key => $value) {
            $keys = explode('.', $key);
            $method = 'field' . Str::camel(end($keys));
            $tmp = $this->$method($value);

            if (! $tmp instanceof DummyModifier){
                $value = $tmp;
            }

            Arr::set($array, $key, $value);
        }

        return $array;
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'field') && method_exists($this, $name)) {
            return $this->{$name}($arguments[0]);
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $name
        ));
    }
}