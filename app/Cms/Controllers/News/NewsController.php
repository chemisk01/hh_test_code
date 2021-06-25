<?php

namespace Cms\Controllers\News;

use Cms\Services\Share\CommonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Cms\Services\News\NewsService;
use Cms\Factories\News\NewsRequestFactory;
use Cms\FormRequests\News\NewsStoreRequest;
use Cms\Resources\News\NewsResource;
use LMSCore\Infrastructure\Exceptions\NotFoundException;

class NewsController extends Controller
{
    protected NewsRequestFactory $newsRequestFactory;
    protected NewsService $newsService;
    protected CommonService $cmsCommonService;

    public function __construct(
        NewsRequestFactory $newsRequestFactory,
        NewsService $newsService,
        CommonService $cmsCommonService
    ) {
        $this->newsRequestFactory = $newsRequestFactory;
        $this->newsService = $newsService;
        $this->cmsCommonService = $cmsCommonService;
    }

    /**
     * Возвращает список новостей с их данными
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $indexRequest = $this->newsRequestFactory->makeIndexRequest($request);

        $result = $this->newsService->getNews($indexRequest, $request->input('filter_by'));

        return NewsResource::collection($result);
    }

    /**
     * Возвращает новость по ее id
     *
     * @param int $id
     * @return NewsResource|JsonResponse
     * @throws \Throwable
     */
    public function show(int $id)
    {
        $result = $this->newsService->getNewsById($id);

        return NewsResource::make($result);
    }

    /**
     * Создание новости
     *
     * @param NewsStoreRequest $request
     * @return NewsResource
     * @throws \Throwable
     */
    public function store(NewsStoreRequest $request): NewsResource
    {
        $request = $this->newsRequestFactory->makeStoreRequest($request);

        $result = $this->newsService->createNews(
            $request
        );

        return NewsResource::make($result);
    }

    /**
     * Обновление новости
     *
     * @param NewsStoreRequest $request
     * @param int $id
     * @return NewsResource
     * @throws \Throwable
     */
    public function update(NewsStoreRequest $request, int $id): NewsResource
    {
        $request = $this->newsRequestFactory->makeUpdateRequest($request, $id);

        $result = $this->newsService->updateNews(
            $request
        );

        return NewsResource::make($result);
    }

    /**
     * Удаление новости
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->newsService->destroyNews($id);

        return response()->json(['data' => ['status' => true, 'message' => 'News with id #' . $id . ' was deleted']]);
    }

    /**
     * Преобразует строку с текстом в IRI
     *
     * @param Request $request
     * @return string
     */
    public function generateIri(Request $request)
    {
        $str = $request->input('string');

        return response()->json(
            ['data' => ['status' => true, 'result' => $this->cmsCommonService->generateIriFromString($str)]]
        );
    }
}
