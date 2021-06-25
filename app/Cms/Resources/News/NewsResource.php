<?php

namespace Cms\Resources\News;

use Cms\Models\News as EloquentNews;
use Cms\Services\Share\CommonService;
use Cms\ValueObjects\News\NewsMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lms\Application\Files\FileTrait;

class NewsResource extends JsonResource
{
    use FileTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var EloquentNews $news */
        $news = $this->resource;

        $result = [
            'id' => $news->getKey(),
            'title' => $news->title,
            'author' => $news->author,
            'description_short' => $news->description_short,
            'description' => $news->description,
            'iri' => $news->iri,
            'page_url' => CommonService::generatePageUrlByIri('news', $news->iri),
            'is_active' => $news->is_active,
            'status' => $news->status,
        ];

        $result['created_at'] = $news->created_at->toDateTimeString();

        $result = array_merge($result, $this->getMetaData($news->meta));

        return $result;
    }

    /**
     * @param NewsMeta $meta
     * @return array
     */
    protected function getMetaData(NewsMeta $meta): array
    {
        $result = [
            'source' => $meta->getSource(),
            'is_pinned' => $meta->getIsPinned(),
            'image_uuid' => $meta->getImageUuid(),
            'image_url' => $this->getFileUrlByUuid($meta->getImageUuid()),
        ];

        return $result;
    }
}
