<?php

declare(strict_types=1);

namespace Cms\Specifications\Share;

use EduPlatform\Request\IndexRequest;

class SpecificationFactory
{
    public function index(IndexRequest $request): array
    {
        $specifications = [];
        if ($request->getWhereList()) {
            $specifications[] = new WhereSpecification($request->getWhereList());
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
