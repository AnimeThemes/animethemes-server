<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Models\BaseModel;
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Artist::ATTRIBUTE_NAME)) {
            $result[Artist::ATTRIBUTE_NAME] = $this->name;
        }

        if ($this->isAllowedField(Artist::ATTRIBUTE_SLUG)) {
            $result[Artist::ATTRIBUTE_SLUG] = $this->slug;
        }

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

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        $result[Artist::RELATION_SONGS] = new SongCollection($this->whenLoaded(Artist::RELATION_SONGS), $this->query);
        $result[Artist::RELATION_MEMBERS] = new ArtistCollection($this->whenLoaded(Artist::RELATION_MEMBERS), $this->query);
        $result[Artist::RELATION_GROUPS] = new ArtistCollection($this->whenLoaded(Artist::RELATION_GROUPS), $this->query);
        $result[Artist::RELATION_RESOURCES] = new ExternalResourceCollection($this->whenLoaded(Artist::RELATION_RESOURCES), $this->query);
        $result[Artist::RELATION_IMAGES] = new ImageCollection($this->whenLoaded(Artist::RELATION_IMAGES), $this->query);

        return $result;
    }
}
