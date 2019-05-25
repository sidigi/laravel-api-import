<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Modificators;

class FieldsModificator implements ModificatorInterface
{
    protected $only;
    protected $except;
    protected $replace;

    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

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

    public function get(): array
    {
        $data = new KeysModificator($this->fields);

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
