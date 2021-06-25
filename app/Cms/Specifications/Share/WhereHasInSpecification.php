<?php

namespace Cms\Specifications\Share;

use Cms\Requests\Share\WhereHasIn;
use Cms\Requests\Share\WhereHasInList;
use Illuminate\Database\Eloquent\Builder;
use LMSCore\Infrastructure\Content\Db\Eloquent\Specifications\SpecificationInterface;

class WhereHasInSpecification implements SpecificationInterface
{
    protected ?WhereHasInList $whereHasInList;

    public function __construct(?WhereHasInList $whereList)
    {
        $this->whereHasInList = $whereList;
    }

    public function apply(Builder $query): Builder
    {
        if (is_null($this->whereHasInList)) {
            return $query;
        }

        /** @var WhereHasIn $whereHasIn */
        foreach ($this->whereHasInList as $whereHasIn) {
            $query->whereHas($whereHasIn->getEntity(), function ($query) use ($whereHasIn) {
                $query->whereIn($whereHasIn->getField(), $whereHasIn->getValues());
            });
        }

        return $query;
    }
}
