<?php

namespace Cms\Specifications\Share;

use EduPlatform\Request\Collection\OrderList;
use EduPlatform\Request\Options\Order;
use Illuminate\Database\Eloquent\Builder;
use LMSCore\Infrastructure\Content\Db\Eloquent\Specifications\SpecificationInterface;

class OrderSpecification implements SpecificationInterface
{
    protected ?OrderList $orderList;

    public function __construct(?OrderList $orderList)
    {
        $this->orderList = $orderList;
    }

    public function apply(Builder $query): Builder
    {
        if (is_null($this->orderList)) {
            return $query;
        }

        $availableOperators = ['asc', 'desc'];
        /** @var Order $order */
        foreach ($this->orderList as $order) {
            if (!in_array($order->getOperator(), $availableOperators, true)) {
                continue;
            }

            $query->orderBy($order->getField(), $order->getOperator());
        }

        return $query;
    }
}
