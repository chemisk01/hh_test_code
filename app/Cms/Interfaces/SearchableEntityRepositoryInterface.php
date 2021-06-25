<?php

namespace Cms\Interfaces;

use EduPlatform\Request\Options\Pagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SearchableEntityRepositoryInterface
{
    public function query(array $specifications, ?Pagination $pagination): LengthAwarePaginator;
}
