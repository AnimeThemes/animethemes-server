<?php

declare(strict_types=1);

namespace App\Search\Builders;

use App\Contracts\Search\SearchBuilder;
use App\Search\Criteria;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
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

    /**
     * @param  array<string, array{direction: string, isString: bool, relation: ?string}>  $sorts
     */
    public function withSort(array $sorts): static
    {
        foreach ($sorts as $column => $data) {
            $this->builder->orderBy($column, Arr::get($data, 'direction'));
        }

        return $this;
    }

    /**
     * Run a callback through the Eloquent query.
     *
     * @param  Closure(EloquentBuilder): void  $callback
     */
    public function passToEloquentBuilder(Closure $callback): SearchBuilder
    {
        $this->builder->query($callback);

        return $this;
    }

    /**
     * Execute the search and get the resulting models.
     */
    public function execute(): Paginator
    {
        return $this->builder
            ->paginate($this->perPage, page: $this->page);
    }

    /**
     * Get the keys of the retrieved models.
     *
     * @return int[]
     */
    public function keys(): array
    {
        return Arr::pluck($this->execute()->items(), fn (Model $model) => $model->getKey());
    }
}
