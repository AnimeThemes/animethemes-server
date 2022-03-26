<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Song;
use App\Pivots\ArtistSong;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SongResource.
 *
 * @mixin Song
 */
class SongResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'song';

    /**
     * Create a new resource instance.
     *
     * @param  Song | MissingValue | null  $song
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Song|MissingValue|null $song, ReadQuery $query)
    {
        parent::__construct($song, $query);
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

        if ($this->isAllowedField(Song::ATTRIBUTE_TITLE)) {
            $result[Song::ATTRIBUTE_TITLE] = $this->title;
        }

        if ($this->isAllowedField(ArtistSong::ATTRIBUTE_AS)) {
            $result[ArtistSong::ATTRIBUTE_AS] = $this->whenPivotLoaded(ArtistSong::TABLE, fn () => $this->pivot->getAttribute(ArtistSong::ATTRIBUTE_AS));
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

        $result[Song::RELATION_ANIMETHEMES] = ThemeCollection::make($this->whenLoaded(Song::RELATION_ANIMETHEMES), $this->query);
        $result[Song::RELATION_ARTISTS] = ArtistCollection::make($this->whenLoaded(Song::RELATION_ARTISTS), $this->query);

        return $result;
    }
}
