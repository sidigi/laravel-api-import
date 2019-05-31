<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers;

use Illuminate\Support\Arr;

class ExceptModifier implements ModifierInterface
{
    private $keys;

    public function __construct($keys)
    {
        $this->keys = is_array($keys) ? $keys : func_get_args();
    }

    public function modify(array $data): array
    {
        Arr::forget($data, $this->keys);

        return $data;
    }
}