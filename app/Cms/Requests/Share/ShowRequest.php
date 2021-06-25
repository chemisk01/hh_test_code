<?php

namespace Cms\Requests\Share;

use EduPlatform\Request\Collection\IncludesList;

class ShowRequest
{
    private ?IncludesList $includesList;

    public function __construct(
        ?IncludesList $includesList
    ) {
        $this->includesList = $includesList;
    }

    public function getIncludesList(): ?IncludesList
    {
        return $this->includesList;
    }
}
