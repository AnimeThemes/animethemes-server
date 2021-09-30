<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
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
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            Series::ATTRIBUTE_NAME => $this->when($this->isAllowedField(Series::ATTRIBUTE_NAME), $this->name),
            Series::ATTRIBUTE_SLUG => $this->when($this->isAllowedField(Series::ATTRIBUTE_SLUG), $this->slug),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            Series::RELATION_ANIME => AnimeCollection::make($this->whenLoaded(Series::RELATION_ANIME), $this->query),
        ];
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new SeriesSchema();
    }
}
