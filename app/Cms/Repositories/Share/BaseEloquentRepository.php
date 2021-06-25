<?php

declare(strict_types=1);

namespace Cms\Repositories\Share;

use Cms\Specifications\Share\IncludesSpecification;
use EduPlatform\Request\Options\Pagination;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use LMSCore\Infrastructure\Exceptions\NotFoundException;

class BaseEloquentRepository
{
    protected Model $model;
    protected string $modelName;

    public function __construct(Model $model)
    {
        $this->model = $model;

        $modelClass = get_class($model);
        $this->modelName = substr($modelClass, strrpos($modelClass, '\\') + 1);
    }

    /**
     * @param array $specifications
     * @param Pagination|null $pagination
     * @return LengthAwarePaginator
     */
    public function query(array $specifications, ?Pagination $pagination): LengthAwarePaginator
    {
        $query = $this->model::query();

        $query = $this->applySpecifications($query, $specifications);

        $eloquentModels = $this->getEloquentModels($query, $pagination);

        return $eloquentModels;
    }

    /**
     * Применяет переданные спецификации (условия) к запросу
     *
     * @param Builder $query
     * @param array $specifications
     * @return Builder
     */
    protected function applySpecifications(Builder $query, array $specifications): Builder
    {
        foreach ($specifications as $specification) {
            $query = $specification->apply($query);
        }

        return $query;
    }

    /**
     * Возвращает коллекцию моделей с пагинацией из запроса
     *
     * @param Builder $query
     * @param Pagination|null $pagination
     * @return LengthAwarePaginator
     */
    protected function getEloquentModels(Builder $query, ?Pagination $pagination): LengthAwarePaginator
    {
        if (is_null($pagination)) {
            $eloquentModels = $query->paginate();
        } else {
            $eloquentModels = $query->paginate(
                $pagination->getPerPage(),
                ['*'],
                'page',
                $pagination->getPage()
            );
        }

        return $eloquentModels;
    }

    /**
     * @param int $id
     * @param IncludesSpecification|null $includesSpecification
     * @return mixed
     * @throws NotFoundException
     */
    public function getById(int $id, ?IncludesSpecification $includesSpecification = null)
    {
        /** @var Builder $query */
        $query = $this->model::select('*');

        if ($includesSpecification) {
            $query = $this->applySpecifications($query, [$includesSpecification]);
        }

        /** @var Model|null $model */
        $model = $query->find($id);

        if (is_null($model)) {
            throw new NotFoundException(
                'Entity of "' . get_class($this->model) . '" class with id #' . $id . ' not found'
            );
        }

        return $model;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        $model = $this->model::create($attributes);
        $model->save();

        return $model;
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return mixed
     * @throws NotFoundException
     */
    public function update(int $id, array $attributes)
    {
        $model = $this->getById($id);
        $model->update($attributes);

        return $model;
    }

    /**
     * @param int $id
     * @throws NotFoundException
     * @throws Exception
     */
    public function remove(int $id): void
    {
        $model = $this->getById($id);
        $model->delete();
    }
}
