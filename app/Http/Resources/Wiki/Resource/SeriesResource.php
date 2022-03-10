<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Series;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SeriesResource.
 *
 * @mixin Series
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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Series|MissingValue|null $series, Query $query)
    {
        parent::__construct($series, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Series::ATTRIBUTE_NAME)) {
            $result[Series::ATTRIBUTE_NAME] = $this->name;
        }

        if ($this->isAllowedField(Series::ATTRIBUTE_SLUG)) {
            $result[Series::ATTRIBUTE_SLUG] = $this->slug;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        $result[Series::RELATION_ANIME] = AnimeCollection::make($this->whenLoaded(Series::RELATION_ANIME), $this->query);

        return $result;
    }
}
