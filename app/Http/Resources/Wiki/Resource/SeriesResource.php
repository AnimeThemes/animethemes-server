<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Models\Wiki\Series;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SeriesResource.
 */
class SeriesResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'series';

    /**
     * Create a new resource instance.
     *
     * @param  Series | MissingValue | null  $series
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Series|MissingValue|null $series, ReadQuery $query)
    {
        parent::__construct($series, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[Series::RELATION_ANIME] = new AnimeCollection($this->whenLoaded(Series::RELATION_ANIME), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new SeriesSchema();
    }
}
