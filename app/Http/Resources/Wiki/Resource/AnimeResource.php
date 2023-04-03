<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\BaseResource;
use App\Models\Wiki\Anime;
use App\Pivots\Wiki\AnimeResource as AnimeResourcePivot;
use Illuminate\Http\Request;

/**
 * Class AnimeResource.
 *
 * @mixin Anime
 */
class AnimeResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'anime';

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
            $result[AnimeResourcePivot::ATTRIBUTE_AS] = $this->whenPivotLoaded(AnimeResourcePivot::TABLE, fn () => $this->pivot->getAttribute(AnimeResourcePivot::ATTRIBUTE_AS));
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
        return new AnimeSchema();
    }
}
