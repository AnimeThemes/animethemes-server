<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Studio;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class StudioResource.
 *
 * @mixin Studio
 */
class StudioResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'studio';

    /**
     * Create a new resource instance.
     *
     * @param  Studio | MissingValue | null  $studio
     * @param  Query  $query
     * @return void
     */
    public function __construct(Studio|MissingValue|null $studio, Query $query)
    {
        parent::__construct($studio, $query);
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
            Studio::ATTRIBUTE_NAME => $this->when($this->isAllowedField(Studio::ATTRIBUTE_NAME), $this->name),
            Studio::ATTRIBUTE_SLUG => $this->when($this->isAllowedField(Studio::ATTRIBUTE_SLUG), $this->slug),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            Studio::RELATION_ANIME => AnimeCollection::make($this->whenLoaded(Studio::RELATION_ANIME), $this->query),
        ];
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new StudioSchema();
    }
}
