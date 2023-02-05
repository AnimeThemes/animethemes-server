<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistResource as ArtistResourcePivot;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

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
     * Create a new resource instance.
     *
     * @param  Artist | MissingValue | null  $artist
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Artist|MissingValue|null $artist, ReadQuery $query)
    {
        parent::__construct($artist, $query);
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

        $result[Artist::RELATION_SONGS] = new SongCollection($this->whenLoaded(Artist::RELATION_SONGS), $this->query);
        $result[Artist::RELATION_MEMBERS] = new ArtistCollection($this->whenLoaded(Artist::RELATION_MEMBERS), $this->query);
        $result[Artist::RELATION_GROUPS] = new ArtistCollection($this->whenLoaded(Artist::RELATION_GROUPS), $this->query);
        $result[Artist::RELATION_RESOURCES] = new ExternalResourceCollection($this->whenLoaded(Artist::RELATION_RESOURCES), $this->query);
        $result[Artist::RELATION_IMAGES] = new ImageCollection($this->whenLoaded(Artist::RELATION_IMAGES), $this->query);

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
