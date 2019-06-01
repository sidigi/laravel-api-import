<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use sidigi\LaravelApiImport\Exceptions\InvalidModifierClassException;
use sidigi\LaravelApiImport\Modifiers\Arr\ArrModifierInterface;
use sidigi\LaravelApiImport\Modifiers\Value\ValueModifierInterface;

class Modifier
{
    public static $defaultModifiers = [];
    public static $entityModifiers = [];

    public static function defaultModifiers(array $defaultModifiers): void
    {
        static::$defaultModifiers = array_merge(static::$defaultModifiers, $defaultModifiers);
    }

    public static function modifiersForEntity($class, array $modifiers): void
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        static::$entityModifiers[$class] = $modifiers;
    }

    public static function availableInterfaces(): array
    {
        return [ValueModifierInterface::class, ArrModifierInterface::class];
    }

    public function availableModifiers($class, string $filterByInterface): array
    {
        return array_merge($this->get($filterByInterface), $this->getForEntity($class, $filterByInterface));
    }

    public function get(string $filterByInterface): array
    {
        return $this->filter(static::$defaultModifiers, $filterByInterface);
    }

    public function getForEntity($class, string $filterByInterface): array
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        return $this->filter(static::$entityModifiers[$class], $filterByInterface);
    }

    private function filter(array $array, string $filterByInterface): array
    {
        return collect($array)->filter(function ($modifier) use ($filterByInterface){
            $this->implementingNeededInterfaceCheck($modifier);

            return in_array($filterByInterface, class_implements($modifier), true);
        })->toArray();
    }

    /**
     * @param $modifier
     * @return bool
     * @throws InvalidModifierClassException
     */
    private function implementingNeededInterfaceCheck($modifier): bool
    {
        if (! array_intersect(class_implements($modifier), static::availableInterfaces())){
            $error = vsprintf('The given modifier class [%s] must be one of [%s]', [
                $modifier,
                implode(', ', static::availableInterfaces())
            ]);

            throw new InvalidModifierClassException($error);
        }

        return true;
    }
}