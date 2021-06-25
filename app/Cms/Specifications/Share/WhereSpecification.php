<?php

namespace Cms\Specifications\Share;

use EduPlatform\Request\Collection\WhereList;
use EduPlatform\Request\Options\Where;
use Illuminate\Database\Eloquent\Builder;
use LMSCore\Infrastructure\Content\Db\Eloquent\Specifications\SpecificationInterface;

class WhereSpecification implements SpecificationInterface
{
    protected ?WhereList $whereList;

    public function __construct(?WhereList $whereList)
    {
        $this->whereList = $whereList;
    }

    public function apply(Builder $query): Builder
    {
        if (is_null($this->whereList)) {
            return $query;
        }

        $availableOperators = ['=', '!=', 'like', '<', '>', '<=', '>='];
        /** @var Where $where */
        foreach ($this->whereList as $where) {
            if (!in_array($where->getOperator(), $availableOperators, true)) {
                continue;
            }

            $query->where($where->getField(), $where->getOperator(), $where->getValue());
        }

        return $query;
    }
}
