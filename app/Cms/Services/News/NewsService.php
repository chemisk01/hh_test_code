<?php

declare(strict_types=1);

namespace Cms\Services\News;

use Cms\Interfaces\SearchableEntityInterface;
use Cms\Interfaces\SearchableEntityRepositoryInterface;
use Cms\Models\News as EloquentNews;
use Cms\Repositories\News\NewsRepository;
use Cms\Requests\News\NewsStatusUpdateRequest;
use Cms\Requests\News\NewsStoreRequest;
use Cms\Services\Share\CommonService;
use Cms\Services\Tags\TagService;
use Cms\Specifications\News\NewsFilterSpecification;
use Cms\Specifications\Share\SpecificationFactory;
use EduPlatform\Request\IndexRequest;
use EduPlatform\Request\Options\Where;
use EduShare\Application\TransactionManager;
use LMSCore\Infrastructure\Exceptions\NotFoundException;
use LMSCore\Infrastructure\Share\Db\Specifications\WhereSpecification;

class NewsService implements SearchableEntityInterface
{
    protected TransactionManager $transactionManager;
    protected SpecificationFactory $specificationFactory;
    protected NewsRepository $newsRepository;
    protected TagService $tagService;

    public function __construct(
        TransactionManager $transactionManager,
        SpecificationFactory $specificationFactory,
        NewsRepository $newsRepository,
        TagService $tagService
    ) {
        $this->transactionManager = $transactionManager;
        $this->specificationFactory = $specificationFactory;
        $this->newsRepository = $newsRepository;
        $this->tagService = $tagService;
    }

    /**
     * @param IndexRequest $request
     * @param array|null $filterBy
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getNews(IndexRequest $request, ?array $filterBy)
    {
        $specifications = $this->specificationFactory->index($request);

        if (!empty($filterBy) && is_array($filterBy)) {
            foreach ($filterBy as $filterKey => $filterValue) {
                $specifications[] = new NewsFilterSpecification((string)$filterKey, $filterValue);
            }
        }

        return $this->newsRepository->query($specifications, $request->getPagination());
    }

    /**
     * @param int $id
     * @return EloquentNews
     * @throws \Throwable
     */
    public function getNewsById(int $id): EloquentNews
    {
        return $this->newsRepository->getById($id);
    }

    /**
     * Получение новости по ее IRI
     *
     * @param string $iri
     * @return EloquentNews
     * @throws \Throwable
     */
    public function getByIri(string $iri): EloquentNews
    {
        return $this->newsRepository->getByIri($iri);
    }

    /**
     * @param NewsStoreRequest $request
     * @return EloquentNews
     * @throws \Throwable
     */
    public function createNews(
        NewsStoreRequest $request
    ): EloquentNews {
        $this->transactionManager->begin();

        try {
            $news = $this->newsRepository->create($request);

            // Update tags
            $this->tagService->updateTags($news, $request->getTags());

            $this->transactionManager->commit();
        } catch (\Throwable $e) {
            $this->transactionManager->rollback();
            throw $e;
        }

        return $news;
    }

    /**
     * @param NewsStoreRequest $request
     * @return EloquentNews
     * @throws \Throwable
     */
    public function updateNews(
        NewsStoreRequest $request
    ): EloquentNews {
        $this->transactionManager->begin();

        try {
            $news = $this->newsRepository->update($request);

            // Update tags
            $this->tagService->updateTags($news, $request->getTags());

            $this->transactionManager->commit();
        } catch (\Throwable $e) {
            $this->transactionManager->rollback();
            throw $e;
        }

        return $news;
    }

    /**
     * @param int $id
     * @throws \Throwable
     */
    public function destroyNews(int $id): void
    {
        $this->newsRepository->remove($id);
    }

    /**
     * @param NewsStatusUpdateRequest $statusUpdateRequest
     * @return EloquentNews
     * @throws \Throwable
     */
    public function updateNewsStatus(NewsStatusUpdateRequest $statusUpdateRequest): EloquentNews
    {
        $this->transactionManager->begin();

        try {
            $statusId = $statusUpdateRequest->getStatusId();

            if (!$this->isNewsStatusValid($statusId)) {
                throw new \Exception('Недопустимый статус новости: ' . $statusId);
            }

            $news = $this->newsRepository->updateStatus(
                $statusUpdateRequest->getNewsId(),
                $statusId
            );

            $this->transactionManager->commit();
        } catch (NotFoundException $e) {
            $this->transactionManager->rollback();
            throw $e;
        }

        return $news;
    }

    /**
     * @param int $statusId
     * @return bool
     */
    protected function isNewsStatusValid(int $statusId): bool
    {
        return in_array($statusId, EloquentNews::getNewsStatuses());
    }

    /**
     * @return SearchableEntityRepositoryInterface
     */
    public function getRepository(): SearchableEntityRepositoryInterface
    {
        return $this->newsRepository;
    }

    /**
     * Условия поиска: заголовок, анонс, текст новости
     *
     * @return string[]
     */
    public function getSearchFields(): array
    {
        return [
            'title',
            'description_short',
            'description',
        ];
    }

    /**
     * Дополнительные условия поиска (статус, активность и т.п.)
     *
     * @return array
     */
    public function getAdditionalSpecificationsForSearch(): array
    {
        return [
            new WhereSpecification(new Where('is_active', 1)),
        ];
    }

    /**
     * Возвращает необходимую информацию о сущности
     *
     * @param object $entity
     * @return array
     */
    public function getSearchItemData(object $entity): array
    {
        return [
            'id' => $entity->getKey(),
            'title' => $entity->title,
            'description_short' => $entity->description_short,
            'description' => $entity->description,
            'page_url' => CommonService::generatePageUrlByIri('news', $entity->iri),
            'iri' => $entity->iri,
        ];
    }
}
