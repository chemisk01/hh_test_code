<?php

namespace Cms\Specifications\News;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use LMSCore\Infrastructure\Content\Db\Eloquent\Specifications\SpecificationInterface;

class NewsFilterSpecification implements SpecificationInterface
{
    public const NEWS_AUTHOR_FAR = 'фар';

    protected ?string $filterKey;
    protected ?string $filterValue;

    public function __construct(string $filterKey, string $filterValue)
    {
        $this->filterKey = $filterKey;
        $this->filterValue = $filterValue;
    }

    public function apply(Builder $query): Builder
    {
        $filterValue = $this->filterValue ?? '';

        switch ($this->filterKey) {
            case 'author':
                $this->applyAuthorCnd($query, $filterValue);

                break;
            case 'created_date':
                $this->applyCreatedDateCnd($query, $filterValue);

                break;
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    protected function applyAuthorCnd(Builder $query, string $value): Builder
    {
        switch ($value) {
            case 'far':
                $query->where('author', 'LIKE', self::NEWS_AUTHOR_FAR);
                break;

            case 'not_far':
                $query->where('author', 'NOT LIKE', self::NEWS_AUTHOR_FAR);
                break;
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    protected function applyCreatedDateCnd(Builder $query, string $value): Builder
    {
        $date = null;

        switch ($value) {
            case 'day':
                $date = Carbon::now()->subDay();
                break;

            case 'week':
                $date = Carbon::now()->subWeek();
                break;

            case 'month':
                $date = Carbon::now()->subMonth();
                break;

            case 'year':
                $date = Carbon::now()->subYear();
                break;
        }

        if ($date) {
            $query->where('created_at', '>=', $date);
        }

        return $query;
    }
}
