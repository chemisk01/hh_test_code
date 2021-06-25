<?php

namespace Cms\Controllers\News;

use Cms\Models\News as EloquentNews;
use Cms\Requests\News\NewsStatusUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Cms\Services\News\NewsService;
use Cms\Factories\News\NewsRequestFactory;
use Cms\Resources\News\NewsResource;

class NewsStatusController extends Controller
{
    protected NewsRequestFactory $newsRequestFactory;
    protected NewsService $newsService;

    public function __construct(
        NewsRequestFactory $newsRequestFactory,
        NewsService $newsService
    ) {
        $this->newsRequestFactory = $newsRequestFactory;
        $this->newsService = $newsService;
    }

    /**
     * @param int $id
     * @return NewsResource
     * @throws \Throwable
     */
    public function approve(int $id): NewsResource
    {
        $statusUpdateRequest = new NewsStatusUpdateRequest($id, EloquentNews::STATUS_APPROVED);

        $result = $this->newsService->updateNewsStatus($statusUpdateRequest);

        return NewsResource::make($result);
    }

    /**
     * @param int $id
     * @return NewsResource
     * @throws \Throwable
     */
    public function reject(int $id): NewsResource
    {
        $statusUpdateRequest = new NewsStatusUpdateRequest($id, EloquentNews::STATUS_REJECTED);

        $result = $this->newsService->updateNewsStatus($statusUpdateRequest);

        return NewsResource::make($result);
    }

    /**
     * Установка произвольного статуса
     *
     * @param Request $request
     * @param int $id
     * @return NewsResource
     * @throws \Throwable
     */
    public function updateStatus(Request $request, int $id): NewsResource
    {
        $statusUpdateRequest = $this->newsRequestFactory->makeStatusUpdateRequest($request, $id);

        $result = $this->newsService->updateNewsStatus($statusUpdateRequest);

        return NewsResource::make($result);
    }
}
