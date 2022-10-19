<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Models\BaseModel;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource as AnimeResourcePivot;
use App\Pivots\Wiki\ArtistResource as ArtistResourcePivot;
use App\Pivots\Wiki\StudioResource as StudioResourcePivot;
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
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(ExternalResource|MissingValue|null $resource, ReadQuery $query)
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
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(ExternalResource::ATTRIBUTE_LINK)) {
            $result[ExternalResource::ATTRIBUTE_LINK] = $this->link;
        }

        if ($this->isAllowedField(ExternalResource::ATTRIBUTE_EXTERNAL_ID)) {
            $result[ExternalResource::ATTRIBUTE_EXTERNAL_ID] = $this->external_id;
        }

        if ($this->isAllowedField(ExternalResource::ATTRIBUTE_SITE)) {
            $result[ExternalResource::ATTRIBUTE_SITE] = $this->site?->description;
        }

        if ($this->isAllowedField(AnimeResourcePivot::ATTRIBUTE_AS)) {
            $result[AnimeResourcePivot::ATTRIBUTE_AS] = $this->whenPivotLoaded(
                AnimeResourcePivot::TABLE,
                fn () => $this->pivot->getAttribute(AnimeResourcePivot::ATTRIBUTE_AS),
                $this->whenPivotLoaded(
                    ArtistResourcePivot::TABLE,
                    fn () => $this->pivot->getAttribute(ArtistResourcePivot::ATTRIBUTE_AS),
                    $this->whenPivotLoaded(
                        StudioResourcePivot::TABLE,
                        fn () => $this->pivot->getAttribute(StudioResourcePivot::ATTRIBUTE_AS)
                    )
                )
            );
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

        $result[ExternalResource::RELATION_ARTISTS] = new ArtistCollection($this->whenLoaded(ExternalResource::RELATION_ARTISTS), $this->query);
        $result[ExternalResource::RELATION_ANIME] = new AnimeCollection($this->whenLoaded(ExternalResource::RELATION_ANIME), $this->query);
        $result[ExternalResource::RELATION_STUDIOS] = new StudioCollection($this->whenLoaded(ExternalResource::RELATION_STUDIOS), $this->query);

        return $result;
    }
}
