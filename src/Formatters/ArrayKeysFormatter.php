<?php

declare(strict_types=1);

namespace sidigi\LaravelApiImport\Formatters;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use stdClass;

class ArrayKeysFormatter implements ModificatorInterface
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function except($keys): self
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = $this->data;

        Arr::forget($results, $keys);

        return new self($results);
    }

    public function only($keys): self
    {
        $results = [];

        $input = $this->data;

        $placeholder = new stdClass();

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = data_get($input, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return new self($results);
    }

    public function replace(array $keys): self
    {
        $keys = Arr::dot($keys);
        ksort($keys);

        $data = Arr::dot($this->data);

        foreach (array_reverse($keys) as $key => $value) {
            foreach ($this->getReplacedKeys($key, $keys, $data) as $oldVal => $newVal) {
                $data = json_decode(str_replace('"'.$oldVal.'":', '"'.$newVal.'":', json_encode($data)), true);
            }
        }

        $array = [];
        foreach ($data as $key => $value) {
            Arr::set($array, $key, $value);
        }

        return new self($array);
    }

    public function get(): array
    {
        return $this->data;
    }

    private function getReplacedKeys(string $key, array $keys, array &$data): array
    {
        if (!isset($keys[$key])) {
            return [];
        }

        if (Str::contains($key, '.')) {
            $tmp = explode('.', $key);
            array_pop($tmp);
            $newKeys[$key] = implode('.', $tmp).'.'.$keys[$key];
        } else {
            $newKeys[$key] = $keys[$key];
        }

        if (!is_array(Arr::get($this->data, $key))) {
            return $newKeys;
        }

        $newKeys += $this->getNestedReplacedKeys($key, $keys, $data);

        return $newKeys;
    }

    private function getNestedReplacedKeys(string $key, array $keys, array $data): array
    {
        return array_reduce(
            array_filter(array_keys(Arr::dot($data)), static function ($item) use ($key) {
                return preg_match("/^$key(.*)/i", $item) > 0;
            }),
            static function ($result, $item) use ($key, $keys) {
                $from = '/'.preg_quote($key, '/').'/';
                $result[$item] = preg_replace($from, $keys[$key], $item, 1);

                return $result;
            }, []
        );
    }
}
