<?php

declare(strict_types=1);

namespace Cms\ValueObjects\News;

use Illuminate\Support\Arr;

class NewsMeta
{
    protected ?string $imageUuid; // uuid изображения
    protected ?string $source; // Источник новости
    protected bool $isPinned; // Флаг "закрепленная в ленте новостей"

    public function __construct(array $attributes)
    {
        $this->imageUuid = Arr::get($attributes, 'image_uuid');
        $this->source = Arr::get($attributes, 'source');
        $this->isPinned = (bool)Arr::get($attributes, 'is_pinned');
    }

    public function getImageUuid(): ?string
    {
        return $this->imageUuid;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getIsPinned(): bool
    {
        return $this->isPinned;
    }

    public function toArray(): array
    {
        return [
            'image_uuid' => $this->imageUuid,
            'source' => $this->source,
            'is_pinned' => $this->isPinned,
        ];
    }
}
