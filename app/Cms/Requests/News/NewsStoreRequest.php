<?php

declare(strict_types=1);

namespace Cms\Requests\News;

class NewsStoreRequest
{
    protected ?int $newsId;
    protected array $attributes;

    public function __construct(?int $newsId, array $attributes)
    {
        $this->newsId = $newsId;
        $this->attributes = $attributes;
    }

    public function getNewsId(): ?int
    {
        return $this->newsId;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getTags(): array
    {
        return isset($this->getAttributes()['tags']) ? $this->getAttributes()['tags'] : [];
    }
}
