<?php

namespace Cms\Specifications\Share;

use EduPlatform\Request\Collection\WhereList;
use EduPlatform\Request\Options\Where;
use Illuminate\Database\Eloquent\Builder;
use LMSCore\Infrastructure\Content\Db\Eloquent\Specifications\SpecificationInterface;

class OrWhereSpecification implements SpecificationInterface
{
    protected ?WhereList $whereList;

    public function __construct(?WhereList $whereList)
    {
        $this->whereList = $whereList;
    }

    public function apply(Builder $query): Builder
    {
        $whereList = $this->whereList;

        if (is_null($whereList)) {
            return $query;
        }

        $query->where(function ($q) use ($whereList) {
            /** @var Where $where */
            foreach ($whereList as $where) {
                $q->orWhere($where->getField(), $where->getOperator(), $where->getValue());
            }
        });

        return $query;
    }
}
