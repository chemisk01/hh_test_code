<?php

declare(strict_types=1);

namespace Cms\Specifications\Share;

use Cms\Requests\Share\ExtendedIndexRequest;

class ExtendedSpecificationFactory
{
    public function index(ExtendedIndexRequest $request): array
    {
        $specifications = [];
        if ($request->getWhereList()) {
            $specifications[] = new WhereSpecification($request->getWhereList());
        }

        if ($request->getWhereHasInList()) {
            $specifications[] = new WhereHasInSpecification($request->getWhereHasInList());
        }

        if ($request->getOrderList()) {
            $specifications[] = new OrderSpecification($request->getOrderList());
        }

        if ($request->getIncludesList()) {
            $specifications[] = new IncludesSpecification($request->getIncludesList());
        }

        return $specifications;
    }
}
