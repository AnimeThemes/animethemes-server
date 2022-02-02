<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki;

use App\Http\Api\Criteria\Field\Criteria as FieldCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\Anime\ThemeQuery;
use Illuminate\Support\Arr;

/**
 * Class SearchQuery.
 */
class SearchQuery
{
    /**
     * The list of sparse fieldset criteria to apply to the query.
     *
     * @var FieldCriteria[]
     */
    protected array $fieldCriteria;

    /**
     * @var EloquentQuery[]
     */
    protected array $queries;

    /**
     * Create a new query instance.
     *
     * @param  array  $parameters
     */
    final public function __construct(array $parameters = [])
    {
        $this->fieldCriteria = FieldParser::parse($parameters);

        $this->queries[] = AnimeQuery::make($parameters);
        $this->queries[] = ThemeQuery::make($parameters);
        $this->queries[] = ArtistQuery::make($parameters);
        $this->queries[] = SeriesQuery::make($parameters);
        $this->queries[] = SongQuery::make($parameters);
        $this->queries[] = StudioQuery::make($parameters);
        $this->queries[] = VideoQuery::make($parameters);
    }

    /**
     * Create a new query parser instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Get the field criteria.
     *
     * @param  string  $type
     * @return FieldCriteria|null
     */
    public function getFieldCriteria(string $type): ?FieldCriteria
    {
        return Arr::first($this->fieldCriteria, fn (FieldCriteria $criteria) => $criteria->getType() === $type);
    }

    /**
     * Get the query by class.
     *
     * @param string $queryClass
     * @return EloquentQuery|null
     */
    public function getQuery(string $queryClass): ?EloquentQuery
    {
        return Arr::first($this->queries, fn (EloquentQuery $query) => is_a($query, $queryClass));
    }
}
