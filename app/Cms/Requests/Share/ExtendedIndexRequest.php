<?php

namespace Cms\Requests\Share;

use EduPlatform\Request\Collection\IncludesList;
use EduPlatform\Request\Collection\OrderList;
use EduPlatform\Request\Collection\WhereList;
use EduPlatform\Request\IndexRequest;
use EduPlatform\Request\Options\Pagination;

class ExtendedIndexRequest extends IndexRequest
{
    protected ?WhereHasInList $whereHasInList;

    public function __construct(
        ?WhereList $whereList,
        ?WhereHasInList $whereHasInList,
        ?IncludesList $includesList,
        ?OrderList $orderList,
        ?Pagination $pagination
    ) {
        $this->whereHasInList = $whereHasInList;

        parent::__construct($whereList, $includesList, $orderList, $pagination);
    }

    public function getWhereHasInList(): ?WhereHasInList
    {
        return $this->whereHasInList;
    }
}
