<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Arr;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ReplaceArrModifier implements ArrModifierInterface
{
    private $keys;
    private $rawData;

    public function __construct($keys)
    {
        $this->keys = is_array($keys) ? $keys : func_get_args();
    }

    public function modify(array $data): array
    {
        $this->rawData = $data;

        $keys = Arr::dot($this->keys);
        ksort($keys);

        $data = Arr::dot($data);

        foreach (array_reverse($keys) as $key => $value){
            foreach ($this->getReplacedKeys($key, $keys, $data) as $oldVal => $newVal){
                $data = json_decode(str_replace('"' . $oldVal . '":', '"' . $newVal . '":', json_encode($data)), true);
            }
        }

        $array = [];
        foreach ($data as $key => $value) {
            Arr::set($array, $key, $value);
        }

        return $data;
    }

    private function getReplacedKeys(string $key, array $keys, array &$data): array
    {
        if (!isset($keys[$key])){
            return [];
        }

        if (Str::contains($key, '.')){
            $tmp = explode('.', $key);
            array_pop($tmp);
            $newKeys[$key] = implode('.', $tmp) . '.' . $keys[$key];
        }else{
            $newKeys[$key] = $keys[$key];
        }

        if (! is_array(Arr::get($this->rawData, $key))){
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
            static function ($result, $item) use($key, $keys) {
                $from = '/'.preg_quote($key, '/').'/';
                $result[$item] = preg_replace($from, $keys[$key], $item, 1);

                return $result;
            }, []
        );
    }
}