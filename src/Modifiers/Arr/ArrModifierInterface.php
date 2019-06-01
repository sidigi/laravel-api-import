<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Arr;

interface ArrModifierInterface
{
    public function modify(array $data): array;
}