<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
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
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        if ($this->isAllowedField(ArtistSong::ATTRIBUTE_AS)) {
            $result[ArtistSong::ATTRIBUTE_AS] = $this->whenPivotLoaded(ArtistSong::TABLE, fn () => $this->pivot->getAttribute(ArtistSong::ATTRIBUTE_AS));
        }

        $result[Song::RELATION_ANIMETHEMES] = new ThemeCollection($this->whenLoaded(Song::RELATION_ANIMETHEMES), $this->query);
        $result[Song::RELATION_ARTISTS] = new ArtistCollection($this->whenLoaded(Song::RELATION_ARTISTS), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new SongSchema();
    }
}
