<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Arr;

use Illuminate\Support\Arr;
use stdClass;

class OnlyModifier
{
    private $keys;

    public function __construct($keys)
    {
        $this->keys = is_array($keys) ? $keys : func_get_args();
    }

    public function modify(array $data): array
    {
        $results = [];

        $placeholder = new stdClass;

        foreach ($this->keys as $key) {
            $value = data_get($data, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }
}