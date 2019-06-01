<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Arr;

use Illuminate\Support\Arr;
use sidigi\LaravelApiImport\Exceptions\InvalidModifierClassException;
use sidigi\LaravelApiImport\Modifiers\Value\BooleanModifier;
use sidigi\LaravelApiImport\Modifiers\Value\DummyModifier;
use sidigi\LaravelApiImport\Modifiers\Value\FloatModifier;
use sidigi\LaravelApiImport\Modifiers\Value\NullModifier;
use sidigi\LaravelApiImport\Modifiers\Value\ValueModifierInterface;

class TypeConversationArrModifier implements ArrModifierInterface
{
    protected $modifiers = [
        BooleanModifier::class,
        NullModifier::class,
        FloatModifier::class
    ];

    /**
     * @param array $data
     * @return array
     * @throws InvalidModifierClassException
     */
    public function modify(array $data): array
    {
        $data = Arr::dot($data);

        $array = [];
        foreach ($data as $key => $value) {
            if (is_string($value)){
                $value = $this->modifyValue($value);
            }

            Arr::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * @param string $value
     * @return string
     * @throws InvalidModifierClassException
     */
    private function modifyValue(string $value)
    {
        foreach ($this->modifiers as $modifier){
            /** @var  ValueModifierInterface $obj */
            $obj = new $modifier;

            if (! $obj instanceof ValueModifierInterface){
                throw new InvalidModifierClassException('Modifier class must be of the type ' . ValueModifierInterface::class . ', '.get_class($obj).' given');
            }

            $tmp = $obj->modify($value);

            if (! $tmp instanceof DummyModifier){
                $value = $tmp;
            }
        }

        return $value;
    }
}