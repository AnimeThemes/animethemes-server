<?php

declare(strict_types=1);

namespace App\Search\Builders;

use App\Contracts\Search\SearchBuilder;
use App\Search\Criteria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder;

class CollectionBuilder implements SearchBuilder
{
    protected Builder $builder;

    protected int $perPage = 15;
    protected int $page = 1;

    protected function __construct(protected Model $model) {}

    public static function search(Model $model, Criteria $criteria): SearchBuilder
    {
        $builder = new self($model);

        /** @phpstan-ignore-next-line */
        $builder->builder = $model::search($criteria->getTerm());

        return $builder;
    }

    public function withPagination(int $perPage = 15, int $page = 1): static
    {
        $this->perPage = $perPage;
        $this->page = $page;

        return $this;
    }

    public function toEloquentBuilder(): EloquentBuilder
    {
        return $this->model::query()
            ->whereIn(
                $this->model->getKeyName(),
                $this->keys()
            );
    }

    /**
     * The keys of the retrieved models.
     *
     * @return int[]
     */
    public function keys(): array
    {
        return Arr::pluck($this->paginate()->items(), $this->model->getKeyName());
    }

    protected function paginate(): LengthAwarePaginator
    {
        return $this->builder->paginate($this->perPage, page: $this->page);
    }
}
