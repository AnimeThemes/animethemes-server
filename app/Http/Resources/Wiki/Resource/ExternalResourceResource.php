<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Resources\BaseResource;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource as AnimeResourcePivot;
use App\Pivots\Wiki\ArtistResource as ArtistResourcePivot;
use App\Pivots\Wiki\StudioResource as StudioResourcePivot;
use Illuminate\Http\Request;

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
