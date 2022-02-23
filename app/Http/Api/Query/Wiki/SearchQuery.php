<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Query;
use App\Http\Api\Query\Wiki\Anime\ThemeQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SearchSchema;
use Illuminate\Support\Arr;

/**
 * Class SearchQuery.
 */
class SearchQuery extends Query
{
    /**
     * @var EloquentQuery[]
     */
    protected array $queries;

    /**
     * Create a new query instance.
     *
     * @param  array  $parameters
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);

        $this->queries[] = new AnimeQuery($parameters);
        $this->queries[] = new ThemeQuery($parameters);
        $this->queries[] = new ArtistQuery($parameters);
        $this->queries[] = new SeriesQuery($parameters);
        $this->queries[] = new SongQuery($parameters);
        $this->queries[] = new StudioQuery($parameters);
        $this->queries[] = new VideoQuery($parameters);
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new SearchSchema();
    }

    /**
     * Get the query by class.
     *
     * @param  string  $queryClass
     * @return EloquentQuery|null
     */
    public function getQuery(string $queryClass): ?EloquentQuery
    {
        return Arr::first($this->queries, fn (EloquentQuery $query) => is_a($query, $queryClass));
    }
}
