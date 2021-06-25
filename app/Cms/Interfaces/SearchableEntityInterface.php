<?php

namespace Cms\Interfaces;

interface SearchableEntityInterface
{
    public function getRepository(): SearchableEntityRepositoryInterface;

    public function getSearchFields(): array;

    public function getSearchItemData(object $entity): array;
}
