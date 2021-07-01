<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\ThemeCollection;
use App\Models\Wiki\Song;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class SongResource.
 */
class SongResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'song';

    /**
     * Create a new resource instance.
     *
     * @param Song | MissingValue | null $song
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(Song | MissingValue | null $song, QueryParser $parser)
    {
        parent::__construct($song, $parser);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->song_id),
            'title' => $this->when($this->isAllowedField('title'), strval($this->title)),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('artist_song', function () {
                return strval($this->pivot->as);
            })),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'themes' => ThemeCollection::make($this->whenLoaded('themes'), $this->parser),
            'artists' => ArtistCollection::make($this->whenLoaded('artists'), $this->parser),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'themes',
            'themes.anime',
            'artists',
        ];
    }
}
