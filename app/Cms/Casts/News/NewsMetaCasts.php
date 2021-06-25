<?php

namespace Cms\Casts\News;

use Cms\ValueObjects\News\NewsMeta;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class NewsMetaCasts implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return new NewsMeta((array)json_decode($value));
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return [
            'meta' => json_encode($value),
        ];
    }
}
