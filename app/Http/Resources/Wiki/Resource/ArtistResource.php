<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Artist;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource as ArtistResourcePivot;
use App\Pivots\ArtistSong;
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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Artist|MissingValue|null $artist, Query $query)
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
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            Artist::ATTRIBUTE_NAME => $this->when($this->isAllowedField(Artist::ATTRIBUTE_NAME), $this->name),
            Artist::ATTRIBUTE_SLUG => $this->when($this->isAllowedField(Artist::ATTRIBUTE_SLUG), $this->slug),
            ArtistSong::ATTRIBUTE_AS => $this->when(
                $this->isAllowedField(ArtistSong::ATTRIBUTE_AS),
                $this->whenPivotLoaded(
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
                )
            ),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            Artist::RELATION_SONGS => SongCollection::make($this->whenLoaded(Artist::RELATION_SONGS), $this->query),
            Artist::RELATION_MEMBERS => ArtistCollection::make($this->whenLoaded(Artist::RELATION_MEMBERS), $this->query),
            Artist::RELATION_GROUPS => ArtistCollection::make($this->whenLoaded(Artist::RELATION_GROUPS), $this->query),
            Artist::RELATION_RESOURCES => ExternalResourceCollection::make($this->whenLoaded(Artist::RELATION_RESOURCES), $this->query),
            Artist::RELATION_IMAGES => ImageCollection::make($this->whenLoaded(Artist::RELATION_IMAGES), $this->query),
        ];
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new ArtistSchema();
    }
}
