<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
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
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

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

        $result[ExternalResource::RELATION_ARTISTS] = new ArtistCollection($this->whenLoaded(ExternalResource::RELATION_ARTISTS), $this->query);
        $result[ExternalResource::RELATION_ANIME] = new AnimeCollection($this->whenLoaded(ExternalResource::RELATION_ANIME), $this->query);
        $result[ExternalResource::RELATION_STUDIOS] = new StudioCollection($this->whenLoaded(ExternalResource::RELATION_STUDIOS), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ExternalResourceSchema();
    }
}
