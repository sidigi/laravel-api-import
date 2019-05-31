<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TypeConversionModifier implements ModifierInterface
{
    protected $rules = [
        'true' => true,
        'false' => false,
        'null' => null,
    ];

    protected $types = [
        'float' => true
    ];

    public function modify(array $data): array
    {
        $data = Arr::dot($data);

        $array = [];
        foreach ($data as $key => $value) {
            if (is_string($value)){

                foreach ($this->types as $keyType => $typeVal){

                    $method = 'to' . Str::camel($keyType);
                    if (method_exists($this, $method)){
                        $value = $this->$method($value);
                    }
                }

            }

            //get last key from dot array
            $keyV = explode('.', $key);
            $method = 'field' . Str::camel(array_pop($keyV));
            $value = $this->$method($value);

            Arr::set($array, $key, $value);
        }

        return $array;
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'field')){
            return $this->fieldConversion($arguments[0]);
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $name
        ));
    }

    public function toFloat(string $string)
    {
        if ((string)(float)$string === $string) {
            if (strpos($string, '.') !== false) {
                return (float) $string;
            }

            return (int) $string;
        }

        return $string;
    }

    protected function rules(): array
    {
        return $this->rules;
    }

    protected function types(): array
    {
        return $this->types;
    }

    private function fieldConversion($value)
    {
        return $this->rules[$value] ?? $value;
    }
}