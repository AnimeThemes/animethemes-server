<?php

declare(strict_types=1);

namespace App\Scout;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Scout\Elasticsearch\Elasticsearch;
use App\Scout\Typesense\Typesense;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use RuntimeException;

abstract class Search
{
    public function __construct(protected Criteria $criteria) {}

    /**
     * @param  class-string<Model>|Model  $model
     */
    public static function getSearch(Model|string $model, Criteria $criteria): Search
    {
        $model = $model instanceof Model ? $model : new $model;

        return match ($driver = Config::get('scout.driver')) {
            'elastic' => App::make(Elasticsearch::class, ['criteria' => $criteria]),
            'typesense' => App::make(Typesense::class, ['model' => $model, 'criteria' => $criteria]),
            default => throw new RuntimeException("Unsupported {$driver} search driver configured."),
        };
    }

    /**
     * Should the search be performed?
     */
    abstract public function shouldSearch(): bool;

    /**
     * Perform the search.
     */
    abstract public function searchViaJSONAPI(
        Query $query,
        EloquentSchema $schema,
        PaginationStrategy $paginationStrategy
    ): Collection|Paginator;

    /**
     * @param  Closure(EloquentBuilder): mixed  $callback
     * @param  array<string, array{direction: string, isString: bool, relation: ?string}>  $sorts
     */
    abstract public function search(?Closure $callback = null, int $perPage = 15, int $page = 1, array $sorts = []): LengthAwarePaginator;
}
