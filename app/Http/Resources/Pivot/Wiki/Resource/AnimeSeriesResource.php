<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\AnimeSeriesSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Http\Request;

/**
 * Class AnimeSeriesResource.
 */
class AnimeSeriesResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animeseries';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[AnimeSeries::RELATION_ANIME] = new AnimeResource($this->whenLoaded(AnimeSeries::RELATION_ANIME), $this->query);
        $result[AnimeSeries::RELATION_SERIES] = new SeriesResource($this->whenLoaded(AnimeSeries::RELATION_SERIES), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AnimeSeriesSchema();
    }
}
