<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\BaseResource;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistResource as ArtistResourcePivot;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Http\Request;

/**
 * Class ArtistResource.
 *
 * @mixin Artist
 */
class ArtistResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artist';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        if ($this->isAllowedField(ArtistSong::ATTRIBUTE_AS)) {
            $result[ArtistSong::ATTRIBUTE_AS] = $this->whenPivotLoaded(
                ArtistSong::TABLE,
                fn () => $this->pivot->getAttribute(ArtistSong::ATTRIBUTE_AS),
                $this->whenPivotLoaded(
                    ArtistMember::TABLE,
                    fn () => $this->pivot->getAttribute(ArtistMember::ATTRIBUTE_AS),
                    $this->whenPivotLoaded(
                        ArtistResourcePivot::TABLE,
                        fn () => $this->pivot->getAttribute(ArtistResourcePivot::ATTRIBUTE_AS)
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
        return new ArtistSchema();
    }
}
