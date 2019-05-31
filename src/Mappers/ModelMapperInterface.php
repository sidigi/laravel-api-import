<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport\Mappers;

use Illuminate\Database\Eloquent\Model;

interface ModelMapperInterface
{
    public function fill(Model $model, array $fields): Model;
}