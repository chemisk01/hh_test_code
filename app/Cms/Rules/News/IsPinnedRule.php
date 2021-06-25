<?php

declare(strict_types=1);

namespace Cms\Rules\News;

use Cms\Models\News as EloquentNews;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class IsPinnedRule implements Rule
{
    protected ?int $newsStatusId;

    public function __construct(?int $newsStatusId)
    {
        $this->newsStatusId = $newsStatusId;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value): bool
    {
        if (!$this->newsStatusId) {
            return false;
        }
        if ($value === true && ($this->newsStatusId !== EloquentNews::STATUS_APPROVED)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return 'Нельзя прикрепить не одобренную новость';
    }
}
