<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use Illuminate\Database\Eloquent\Model;
use sidigi\LaravelApiImport\Mappers\EloquentDataMapper;

class ImportModel extends ImportEntity
{
    protected $exists = ['external_id'];
    protected $allowedIds = [];

    protected $model;
    protected $modelDataMapper = EloquentDataMapper::class;
    protected $flags = [];

    public function fire(): void
    {
        if ($this->needTruncate()){
            $this->model::truncate();
        }

        parent::fire();

        if ($this->needDeleteRecordsNotInResponse()){
            $this->model::destroy($this->allowedIds);
        }
    }

    private function getFilled(array $fields): Model
    {
        /** @var Model $model */
        $model = new $this->model;

        if ( ! $this->exists){
            return $this->fill($model, $fields);
        }

        foreach ( (array) $this->exists as $key){
            $where[$key] = $fields[$key];
        }

        return $this->fill(
        $model
            ->newQuery()
            ->where($where ?? [])
            ->first() ?? $model
        , $fields);
    }

    protected function eachRegister(): void
    {
        $this->each(function ($item){
            $result = $this->eachItem($this->modifyItem($item));

            if ($result === false){
                return;
            };

            if (is_array($result)){
                $item = $result;
            }

            $model = $this->getFilled($item);

            if ($this->saving($model, $item) === false){
                return;
            }

            $model->save();

            if ($this->needDeleteRecordsNotInResponse()){
                $this->allowedIds[] = $model->{$model->getKeyName()};
            }
        });
    }

    protected function saving(Model $model, array $item)
    {

    }

    private function fill(Model $model, array $fields): Model
    {
        return app()->make($this->modelDataMapper)->fill($model, $fields);
    }

    private function needTruncate()
    {
        return in_array(Flag::TRUNCATE, $this->flags, true);
    }

    private function needDeleteRecordsNotInResponse()
    {
        return in_array(Flag::DELETE_RECORDS_NOT_IN_RESPONSE, $this->flags, true);
    }
}
