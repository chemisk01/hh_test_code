<?php

declare(strict_types=1);

namespace Cms\Requests\News;

class NewsStatusUpdateRequest
{
    protected int $newsId;
    protected int $statusId;

    public function __construct(int $newsId, int $statusId)
    {
        $this->newsId = $newsId;
        $this->statusId = $statusId;
    }

    public function getNewsId(): int
    {
        return $this->newsId;
    }

    public function getStatusId(): int
    {
        return $this->statusId;
    }
}
