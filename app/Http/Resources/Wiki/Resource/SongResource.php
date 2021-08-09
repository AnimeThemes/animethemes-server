<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\ThemeCollection;
use App\Models\Wiki\Song;
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
     * @param Song | MissingValue | null $song
     * @param Query $query
     * @return void
     */
    public function __construct(Song | MissingValue | null $song, Query $query)
    {
        parent::__construct($song, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->song_id),
            'title' => $this->when($this->isAllowedField('title'), $this->title),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('artist_song', function () {
                return $this->pivot->getAttribute('as');
            })),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'themes' => ThemeCollection::make($this->whenLoaded('themes'), $this->query),
            'artists' => ArtistCollection::make($this->whenLoaded('artists'), $this->query),
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
