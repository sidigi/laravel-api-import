<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modifiers\Arr;

class FieldsArrModifier implements ArrModifierInterface
{
    protected $only;
    protected $except;
    protected $replace;

    public function only(): ?array
    {
        return $this->only;
    }

    public function except(): ?array
    {
        return $this->except;
    }

    public function replace(): ?array
    {
        return $this->replace;
    }

    public function modify(array $data): array
    {

        if ($only = $this->only()){
            $data = (new OnlyModifier($only))->modify($data);
        }

        if ($except = $this->except()){
            $data = (new ExceptArrModifier($except))->modify($data);
        }

        if ($replace = $this->replace()){
            $data = (new ReplaceArrModifier($replace))->modify($data);
        }

        return $data;
    }
}
