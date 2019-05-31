<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Mappers;

use Illuminate\Database\Eloquent\Model;

class EloquentDataMapper implements ModelMapperInterface
{
    public function fill(Model $model, array $fields): Model
    {
        foreach($fields as $key => $field){
            $model->{$key} = $field;
        }

        return $model;
    }
}
