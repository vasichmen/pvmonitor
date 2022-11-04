<?php namespace App\Repositories;

use App\Models\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class AbstractRepository
{
    protected AbstractModel $model;

    public function __construct(AbstractModel $model)
    {
        $this->model = $model;
    }

    /**Собирает запрос в БД по заданным условиям и возвращает перую найденную запись или null
     * @param  array     $filters   фильтры для запроса [поле => значение]
     * @param  array     $with      аналогичен Builder->with()
     * @param  array     $select    аналогичен Builder->select()
     * @param  array     $orderBy   аналогичен Builder->orderBy()
     *                              ограничения по времени. По умолчанию время берется из config('cache.ttl')
     * @param  int|null  $limit     число записей на странице
     * @param  int|null  $offset    номер страницы
     * @return Collection
     */
    public function getList(array $filters = [], array $with = [], array $select = ['*'], array $orderBy = [], ?int $limit = null, ?int $offset = null): Collection
    {
        return $this->buildQuery($filters, $with, $select, $orderBy, $limit, $offset)->get();
    }

    /**Собирает запрос в БД по заданным условиям и возвращает перую найденную запись или null
     * @param  array     $filters   фильтры для запроса [поле => значение]
     * @param  array     $with      аналогичен Builder->with()
     * @param  array     $select    аналогичен Builder->select()
     * @param  array     $orderBy   аналогичен Builder->orderBy()
     *                              ограничения по времени. По умолчанию время берется из config('cache.ttl')
     * @return AbstractModel|null
     */
    public function find(array $filters = [], array $with = [], array $select = ['*'], array $orderBy = []): ?AbstractModel
    {
        return $this->buildQuery($filters, $with, $select, $orderBy)->take(1)->get()->first();
    }

    /**Собирает запрос в БД по заданным условиям
     * @param  array     $filters   фильтры для запроса [поле => значение]
     * @param  array     $with      аналогичен Builder->with()
     * @param  array     $select    аналогичен Builder->select()
     * @param  array     $orderBy   аналогичен Builder->orderBy()
     *                              ограничения по времени. По умолчанию время берется из config('cache.ttl')
     * @param  int|null  $limit     число записей на странице
     * @param  int|null  $offset    номер страницы
     * @return Builder
     */
    private function buildQuery(array $filters = [], array $with = [], array $select = ['*'], array $orderBy = [], ?int $limit = null, ?int $offset = null): Builder
    {
        $query = $this->model;

        $query = $query->with($with)->select($select);

        foreach ($filters as $field => $value) {
            if (is_array($value) || ($value instanceof Collection)) {
                $query = $query->whereIn($field, $value);
            }
            else {
                $query = $query->where($field, $value);
            }
        }

        foreach ($orderBy as $column => $direction) {
            $query = $query->orderBy($column, $direction);
        }

        if (!empty($limit) && !empty($offset)) {
            $query = $query->when($offset, function ($query, $offset) use ($limit) {
                $query->forPage($offset, $limit);
            });
        }

        return $query;
    }

    public function delete(string|AbstractModel $model): ?bool
    {
        $model = $this->getModel($model);
        return $model?->forceDelete();
    }

    public function create(array $params): AbstractModel
    {
        return $this->model->create($params);
    }

    public function update(string|AbstractModel $model, array $params): bool|AbstractModel
    {
        $model = $this->getModel($model);

        if (!empty($model)) {
            $model->fill($params);
            if ($model->save()) {
                return $model;
            }
            return false;
        }
        return false;
    }

    /**Возвращает модель по id. Если передать null или объект, то вернет обратно
     * @param  string|AbstractModel|null  $model
     * @return AbstractModel|null
     */
    public function getModel(string|AbstractModel|null $model): ?AbstractModel
    {
        if (empty($model)) {
            return null;
        }
        if (!($model instanceof AbstractModel)) {
            $model = $this->find(['id' => $model]);
        }
        return $model;
    }
}
