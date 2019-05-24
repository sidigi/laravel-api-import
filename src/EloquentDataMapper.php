<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use Illuminate\Database\Eloquent\Model;

class EloquentDataMapper
{
    protected $entity;
    protected $fields;

    public function __construct(array $fields, Model $model)
    {
        $this->fields = $fields;
        $this->entity = $model;
    }

    public function get(): Model
    {
        $this->fillModel();

        return $this->entity;
    }

    private function fillModel(): void
    {
        foreach($this->fields as $key => $field){
            $this->entity->{$key} = $field;
        }
    }
}
