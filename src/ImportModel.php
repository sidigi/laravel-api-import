<?php
declare(strict_types=1);

namespace sidigi\LaravelApiImport;

use Illuminate\Database\Eloquent\Model;

class ImportModel extends ImportEntity
{
    protected $exists = ['external_id'];
    protected $allowedIds = [];

    protected $model;
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
        $this->each(function ($itemRaw, $response){
            $result = null;

            $item = $itemRaw;

            if ($this->mapper){
                $item = (new $this->mapper($item))->get();
            }

            $model = $this->getFilled($item);

            $result = $this->eachItem($itemRaw, $response);

            if ($result !== false && $this->save($model, $itemRaw) !== false && $this->needDeleteRecordsNotInResponse()){
                $this->allowedIds[] = $model->{$model->getKeyName()};
            }
        });
    }

    protected function save(Model $model, array $item): void
    {
        $model->save();
    }

    private function fill(Model $model, array $fields): Model
    {
        return (new EloquentDataMapper($fields, $model))->get();
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
