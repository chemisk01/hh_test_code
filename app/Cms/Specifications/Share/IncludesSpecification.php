<?php

namespace Cms\Specifications\Share;

use EduPlatform\Request\Collection\IncludesList;
use EduPlatform\Request\Options\Includes;
use Illuminate\Database\Eloquent\Builder;
use LMSCore\Infrastructure\Content\Db\Eloquent\Specifications\SpecificationInterface;

class IncludesSpecification implements SpecificationInterface
{
    protected ?IncludesList $includesList;

    public function __construct(?IncludesList $includesList)
    {
        $this->includesList = $includesList;
    }

    public function apply(Builder $query): Builder
    {
        if (is_null($this->includesList)) {
            return $query;
        }

        /** @var Includes $includes */
        foreach ($this->includesList as $includes) {
            $query->with($includes->getField());
        }

        return $query;
    }
}
