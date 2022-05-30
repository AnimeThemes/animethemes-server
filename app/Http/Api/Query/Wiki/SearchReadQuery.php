<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Query\Wiki\Anime\AnimeReadQuery;
use App\Http\Api\Query\Wiki\Anime\Theme\ThemeReadQuery;
use App\Http\Api\Query\Wiki\Artist\ArtistReadQuery;
use App\Http\Api\Query\Wiki\Series\SeriesReadQuery;
use App\Http\Api\Query\Wiki\Song\SongReadQuery;
use App\Http\Api\Query\Wiki\Studio\StudioReadQuery;
use App\Http\Api\Query\Wiki\Video\VideoReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SearchSchema;
use Illuminate\Support\Arr;

/**
 * Class SearchQuery.
 */
class SearchReadQuery extends ReadQuery
{
    /**
     * @var EloquentReadQuery[]
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

        $this->queries[] = new AnimeReadQuery($parameters);
        $this->queries[] = new ThemeReadQuery($parameters);
        $this->queries[] = new ArtistReadQuery($parameters);
        $this->queries[] = new SeriesReadQuery($parameters);
        $this->queries[] = new SongReadQuery($parameters);
        $this->queries[] = new StudioReadQuery($parameters);
        $this->queries[] = new VideoReadQuery($parameters);
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
     * @return EloquentReadQuery|null
     */
    public function getQuery(string $queryClass): ?EloquentReadQuery
    {
        return Arr::first($this->queries, fn (EloquentReadQuery $query) => is_a($query, $queryClass));
    }
}
