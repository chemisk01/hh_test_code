<?php

declare(strict_types=1);

namespace Cms\Factories\News;

use Carbon\Carbon;
use Cms\Services\Share\CommonService;
use Cms\Requests\News\NewsStatusUpdateRequest;
use Cms\Requests\News\NewsStoreRequest;
use Cms\FormRequests\News\NewsStoreRequest as LaravelStoreRequest;
use EduShare\Infrastructure\Factories\BaseRequestFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class NewsRequestFactory extends BaseRequestFactory
{
    /**
     * @param LaravelStoreRequest $request
     * @return NewsStoreRequest
     */
    public function makeStoreRequest(LaravelStoreRequest $request): NewsStoreRequest
    {
        $attributes = $this->resolveAttributesFromRequest($request);

        return new NewsStoreRequest(null, $attributes);
    }

    /**
     * @param LaravelStoreRequest $request
     * @param int $newsId
     * @return NewsStoreRequest
     */
    public function makeUpdateRequest(LaravelStoreRequest $request, int $newsId): NewsStoreRequest
    {
        $attributes = $this->resolveAttributesFromRequest($request, $newsId);

        return new NewsStoreRequest($newsId, $attributes);
    }

    /**
     * @param Request $request
     * @param int $newsId
     * @return NewsStatusUpdateRequest
     */
    public function makeStatusUpdateRequest(Request $request, int $newsId): NewsStatusUpdateRequest
    {
        $statusId = intval($request->input('status_id'));

        return new NewsStatusUpdateRequest($newsId, $statusId);
    }

    /**
     * Собирает данные из запроса
     *
     * @param LaravelStoreRequest $request
     * @param int|null $newsId
     * @return array
     */
    protected function resolveAttributesFromRequest(LaravelStoreRequest $request, int $newsId = null): array
    {
        $includePostfix = false;

        if (is_null($newsId)) {
            $includePostfix = true;
        }

        $preValidated = $request->validated()['data']['attributes'];

        /** @var CommonService $cmsCommonService */
        $cmsCommonService = resolve(CommonService::class);

        $title = Arr::get($preValidated, 'title');

        $iri = Arr::get($preValidated, 'iri') ?? $cmsCommonService::generateIriFromString($title, $includePostfix);

        $attributes = [
            'title' => $title,
            'author' => Arr::get($preValidated, 'author'),
            'description_short' => Arr::get($preValidated, 'description_short'),
            'description' => Arr::get($preValidated, 'description'),
            'iri' => $iri,
            'is_active' => Arr::get($preValidated, 'is_active'),
            'status' => Arr::get($preValidated, 'status'),
            'meta' => $this->prepareMeta($preValidated),
            'tags' => Arr::get($preValidated, 'tags'),
            'created_at' => Arr::get($preValidated, 'created_at', Carbon::now()),
        ];

        return $attributes;
    }

    /**
     * Собирает данные для поля meta из запроса
     *
     * @param array $preValidated
     * @return array
     */
    protected function prepareMeta(array $preValidated): array
    {
        return [
            'image_uuid' => Arr::get($preValidated, 'image_uuid'),
            'source' => Arr::get($preValidated, 'source'),
            'is_pinned' => Arr::get($preValidated, 'is_pinned', false),
        ];
    }
}
