<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

class Mapper implements EntityMapInterface
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

    public function modifyFields(array $fields): array
    {
        $data = new FieldsDataMapper($fields);

        if ($only = $this->only()){
            $data = $data->only($only);
        }

        if ($except = $this->except()){
            $data = $data->except($except);
        }

        if ($replace = $this->replace()){
            $data = $data->replace($replace);
        }

        return $data->get();
    }
}
