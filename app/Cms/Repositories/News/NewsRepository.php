<?php

declare(strict_types=1);

namespace Cms\Repositories\News;

use Cms\Interfaces\SearchableEntityRepositoryInterface;
use Cms\Models\News as EloquentNews;
use Cms\Requests\News\NewsStoreRequest;
use Cms\Services\Share\CommonService;
use Cms\ValueObjects\News\NewsMeta;
use EduPlatform\Repositories\BaseEloquentRepository;
use EduPlatform\Request\Options\Pagination;
use EduPlatform\Request\Options\Where;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use LMSCore\Infrastructure\Exceptions\NotFoundException;
use LMSCore\Infrastructure\Share\Db\Specifications\WhereSpecification;

/**
 * @property EloquentNews $model
 */
class NewsRepository extends BaseEloquentRepository implements SearchableEntityRepositoryInterface
{
    /**
     * @param int $id
     * @return EloquentNews
     * @throws \Throwable
     */
    public function getById(int $id): EloquentNews
    {
        return $this->getEloquentModel($id);
    }

    /**
     * @param string $iri
     * @return EloquentNews
     * @throws NotFoundException
     */
    public function getByIri(string $iri): EloquentNews
    {
        $eloquentModels = $this->query([
            new WhereSpecification(new Where('iri', '=', $iri))
        ], null);

        /** @var EloquentNews|null $eloquentModel */
        $eloquentModel = $eloquentModels->first();

        if (is_null($eloquentModel)) {
            throw new NotFoundException('News with iri "' . $iri . '" not found');
        }

        return $eloquentModel;
    }

    /**
     * @param ?int $id
     * @return EloquentNews
     * @throws \Throwable
     */
    protected function getEloquentModel(?int $id): EloquentNews
    {
        /** @var EloquentNews|null $eloquentModel */
        $eloquentModel = $this->model->find($id);

        if (is_null($eloquentModel)) {
            throw new NotFoundException('News with id #' . $id . ' not found');
        }

        return $eloquentModel;
    }

    /**
     * @param array $specifications
     * @param Pagination|null $pagination
     * @return LengthAwarePaginator
     */
    public function query(array $specifications, ?Pagination $pagination): LengthAwarePaginator
    {
        $query = $this->model::query();

        foreach ($specifications as $specification) {
            $query = $specification->apply($query);
        }

        if (is_null($pagination)) {
            /** @var LengthAwarePaginator $eloquentModels */
            $eloquentModels = $query->paginate();
        } else {
            $eloquentModels = $query->paginate($pagination->getPerPage(), ['*'], 'page', $pagination->getPage());
        }

        return $eloquentModels;
    }

    /**
     * @param NewsStoreRequest $data
     * @return EloquentNews
     * @throws \Throwable
     */
    public function create(NewsStoreRequest $data): EloquentNews
    {
        try {
            $attributes = $data->getAttributes();

            $attributes['is_active'] = $attributes['is_active'] ?? true;
            $attributes['status'] = $attributes['status'] ?? EloquentNews::STATUS_NEW;

            /** @var EloquentNews $elModel */
            $elModel = $this->model::create($attributes);

            $this->markNewsAsPinned($elModel);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to create news model: ' . $e->getMessage());
        }

        return $elModel;
    }

    /**
     * @param NewsStoreRequest $data
     * @return EloquentNews
     * @throws \Throwable
     */
    public function update(NewsStoreRequest $data): EloquentNews
    {
        try {
            $elModel = $this->getEloquentModel($data->getNewsId());

            /** @var CommonService $commonService */
            $commonService = resolve(CommonService::class);

            $attributes = $data->getAttributes();
            $attributes = $commonService->removeNullValuesFromArray($attributes);

            foreach ($attributes as $k => $v) {
                $elModel->$k = $v;
            }

            $elModel->save();

            $this->markNewsAsPinned($elModel);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to update news model: ' . $e->getMessage());
        }

        return $elModel;
    }

    /**
     * @param int $id
     * @throws \Throwable
     */
    public function remove(int $id): void
    {
        $this->getEloquentModel($id)->delete();
    }

    /**
     * @param int $id
     * @param int $statusId
     * @return EloquentNews
     * @throws \Throwable
     */
    public function updateStatus(int $id, int $statusId): EloquentNews
    {
        $model = $this->getEloquentModel($id);

        $model->fill(['status' => $statusId]);
        $model->save();

        return $model;
    }

    /**
     * @param EloquentNews $news
     * @return EloquentNews
     */
    public function markNewsAsPinned(EloquentNews $news): EloquentNews
    {
        /** @var NewsMeta $meta */
        $meta = $news->meta;

        if ($meta->getIsPinned()) {
            // Сбрасываем флаг у всех новостей
            $this->model->where('meta->is_pinned', '=', true)
                ->update(['meta->is_pinned' => false]);

            // Выставляем флаг у текущей новости
            $this->model->where('news_id', '=', $news->getKey())
                ->update(['meta->is_pinned' => true]);
        }

        return $news;
    }
}
