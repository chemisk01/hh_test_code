<?php

declare(strict_types=1);

namespace Cms\Requests\Share;

use EduPlatform\Request\Collection\BaseRequestCollection;

class WhereHasIn extends BaseRequestCollection
{
    protected string $entity;
    protected string $field;
    protected array $values;

    public function __construct(string $entity, string $field, array $values)
    {
        $this->entity = $entity;
        $this->field = $field;
        $this->values = $values;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
