<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch;

use App\Concerns\Actions\Http\Api\AggregatesFields;
use App\Concerns\Actions\Http\Api\ConstrainsEagerLoads;
use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Scope\ScopeParser;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Video;
use App\Scout\Criteria as SearchCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Filter\Criteria;
use App\Scout\Elasticsearch\Api\Parser\FilterParser;
use App\Scout\Elasticsearch\Api\Parser\PagingParser;
use App\Scout\Elasticsearch\Api\Parser\SortParser;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Scout\Elasticsearch\Api\Query\List\PlaylistQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\SynonymQuery as AnimeSynonymQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\Theme\EntryQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\ThemeQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\AnimeQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\ArtistQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\SeriesQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\SongQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\StudioQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\SynonymQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\VideoQuery;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Search;
use Closure;
use Elastic\Client\ClientBuilderInterface;
use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class Elasticsearch extends Search
{
    use AggregatesFields;
    use ConstrainsEagerLoads;

    protected SearchParametersBuilder $elasticBuilder;

    /**
     * Is the ES instance reachable?
     */
    protected bool $alive;

    public function __construct(ClientBuilderInterface $builder, SearchCriteria $criteria)
    {
        parent::__construct($criteria);

        try {
            $this->alive = $builder->default()->ping()->asBool();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            $this->alive = false;
        }
    }

    /**
     * Is the ES instance reachable?
     */
    public function isAlive(): bool
    {
        return $this->alive;
    }

    /**
     * Should the search be performed?
     */
    public function shouldSearch(): bool
    {
        return $this->isAlive();
    }

    /**
     * Perform the search.
     */
    public function searchViaJSONAPI(
        Query $query,
        EloquentSchema $schema,
        PaginationStrategy $paginationStrategy
    ): Collection|Paginator {
        $elasticSchema = $this->elasticSchema($schema);

        // initialize builder for matches
        $searchBuilder = static::elasticQuery($elasticSchema->model()::class)->build($this->criteria);
        $this->elasticBuilder = $searchBuilder;

        // load aggregate fields
        $this->elasticBuilder->refineModels(function (EloquentBuilder $searchModelBuilder) use ($query, $schema): void {
            $this->withAggregates($searchModelBuilder, $query, $schema);
        });

        // eager load relations with constraints
        $this->elasticBuilder = $this->elasticBuilder->load($this->constrainEagerLoads($query, $schema));

        // apply filters
        $filterBuilder = new BoolQueryBuilder();
        $scope = ScopeParser::parse($schema->type());
        foreach ($query->getFilterCriteria() as $filterCriterion) {
            $elasticFilterCriteria = FilterParser::parse($filterCriterion);
            if ($elasticFilterCriteria instanceof Criteria) {
                foreach ($elasticSchema->filters() as $filter) {
                    if ($filterCriterion->shouldFilter($filter, $scope)) {
                        $filterBuilder = $elasticFilterCriteria->filter($filterBuilder, $filter, $query);
                    }
                }
            }
        }
        try {
            $this->elasticBuilder->postFilter($filterBuilder);
        } catch (QueryBuilderValidationException) {
            // There doesn't appear to be a way to check if any filters have been set in the filter builder
        }

        // apply sorts
        $sorts = [];
        foreach ($query->getSortCriteria() as $sortCriterion) {
            $elasticSortCriteria = SortParser::parse($sortCriterion);
            if ($elasticSortCriteria instanceof Api\Criteria\Sort\Criteria) {
                foreach ($elasticSchema->sorts() as $sort) {
                    if ($sortCriterion->shouldSort($sort, $scope)) {
                        $sorts[] = $elasticSortCriteria->sort($sort);
                    }
                }
            }
        }
        if ($sorts !== []) {
            $this->elasticBuilder->sortRaw($sorts);
        }

        // paginate
        $paginationCriteria = $query->getPagingCriteria($paginationStrategy);
        $elasticPaginationCriteria = PagingParser::parse($paginationCriteria);

        return $elasticPaginationCriteria instanceof Api\Criteria\Paging\Criteria
            ? $elasticPaginationCriteria->paginate($this->elasticBuilder)
            : $this->elasticBuilder->execute()->models();
    }

    /**
     * @param  Closure(EloquentBuilder): void  $callback
     * @param  array<string, array{direction: string, isString: bool, relation: ?string}>  $sorts
     */
    public function search(?Closure $callback = null, int $perPage = 15, int $page = 1, array $sorts = []): LengthAwarePaginator
    {
        // Resolve callback.
        $this->elasticBuilder->refineModels($callback);

        // Resolve sorting.
        $sortRaw = [];
        foreach ($sorts as $column => $data) {
            $nested = Arr::get($data, 'direction');
            if (Arr::get($data, 'isString') === true) {
                if ($relation = Arr::get($data, 'relation')) {
                    $column = $relation.'.'.$column.'_keyword';

                    $nested = [
                        'order' => $nested,
                        'nested' => [
                            'path' => $relation,
                        ],
                    ];
                } else {
                    $column .= '.keyword';
                }
            }

            $sortRaw[$column] = $nested;
        }

        if ($sortRaw !== []) {
            $this->elasticBuilder->sortRaw($sortRaw);
        }

        return $this->elasticBuilder->paginate($perPage, page: $page)->onlyModels();
    }

    /**
     * @param  class-string<Model>  $model
     */
    public static function elasticQuery(string $model): ElasticQuery
    {
        return match ($model) {
            Playlist::class => new PlaylistQuery(),
            AnimeThemeEntry::class => new EntryQuery(),
            AnimeSynonym::class => new AnimeSynonymQuery(),
            AnimeTheme::class => new ThemeQuery(),
            Anime::class => new AnimeQuery(),
            Artist::class => new ArtistQuery(),
            Series::class => new SeriesQuery(),
            Song::class => new SongQuery(),
            Studio::class => new StudioQuery(),
            Synonym::class => new SynonymQuery(),
            Video::class => new VideoQuery(),
            default => throw new RuntimeException("No ElasticQuery defined for model: {$model}"),
        };
    }

    /**
     * Resolve Elasticsearch schema from Eloquent schema.
     */
    private function elasticSchema(EloquentSchema $schema): Schema
    {
        $elasticSchemaClass = Str::of($schema::class)
            ->replace('Http', 'Scout\\Elasticsearch')
            ->__toString();

        return new $elasticSchemaClass();
    }
}
