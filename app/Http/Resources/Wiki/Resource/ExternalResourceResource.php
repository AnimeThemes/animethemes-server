<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\AnimeResource as AnimeResourcePivot;
use App\Pivots\ArtistResource as ArtistResourcePivot;
use App\Pivots\StudioResource as StudioResourcePivot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ExternalResourceResource.
 *
 * @mixin ExternalResource
 */
class ExternalResourceResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'resource';

    /**
     * Create a new resource instance.
     *
     * @param  ExternalResource | MissingValue | null  $resource
     * @param  Query  $query
     * @return void
     */
    public function __construct(ExternalResource|MissingValue|null $resource, Query $query)
    {
        parent::__construct($resource, $query);
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
            ExternalResource::ATTRIBUTE_LINK => $this->when($this->isAllowedField(ExternalResource::ATTRIBUTE_LINK), $this->link),
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => $this->when($this->isAllowedField(ExternalResource::ATTRIBUTE_EXTERNAL_ID), $this->external_id),
            ExternalResource::ATTRIBUTE_SITE => $this->when($this->isAllowedField(ExternalResource::ATTRIBUTE_SITE), $this->site?->description),
            AnimeResourcePivot::ATTRIBUTE_AS => $this->when(
                $this->isAllowedField(AnimeResourcePivot::ATTRIBUTE_AS),
                $this->whenPivotLoaded(
                    AnimeResourcePivot::TABLE,
                    fn () => $this->pivot->getAttribute(AnimeResourcePivot::ATTRIBUTE_AS),
                    $this->whenPivotLoaded(
                        ArtistResourcePivot::TABLE,
                        fn () => $this->pivot->getAttribute(ArtistResourcePivot::ATTRIBUTE_AS),
                        $this->whenPivotLoaded(
                            StudioResourcePivot::TABLE,
                            fn() => $this->pivot->getAttribute(StudioResourcePivot::ATTRIBUTE_AS)
                        )
                    )
                )
            ),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            ExternalResource::RELATION_ARTISTS => ArtistCollection::make($this->whenLoaded(ExternalResource::RELATION_ARTISTS), $this->query),
            ExternalResource::RELATION_ANIME => AnimeCollection::make($this->whenLoaded(ExternalResource::RELATION_ANIME), $this->query),
            ExternalResource::RELATION_STUDIOS => StudioCollection::make($this->whenLoaded(ExternalResource::RELATION_STUDIOS), $this->query)
        ];
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new ExternalResourceSchema();
    }
}
