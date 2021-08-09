<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Models\Wiki\Artist;
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
     * @param Artist | MissingValue | null $artist
     * @param Query $query
     * @return void
     */
    public function __construct(Artist | MissingValue | null $artist, Query $query)
    {
        parent::__construct($artist, $query);
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
            'id' => $this->when($this->isAllowedField('id'), $this->artist_id),
            'name' => $this->when($this->isAllowedField('name'), $this->name),
            'slug' => $this->when($this->isAllowedField('slug'), $this->slug),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('artist_song', function () {
                return $this->pivot->getAttribute('as');
            }, $this->whenPivotLoaded('artist_member', function () {
                return $this->pivot->getAttribute('as');
            }, $this->whenPivotLoaded('artist_resource', function () {
                return $this->pivot->getAttribute('as');
            })))),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'songs' => SongCollection::make($this->whenLoaded('songs'), $this->query),
            'members' => ArtistCollection::make($this->whenLoaded('members'), $this->query),
            'groups' => ArtistCollection::make($this->whenLoaded('groups'), $this->query),
            'resources' => ExternalResourceCollection::make($this->whenLoaded('resources'), $this->query),
            'images' => ImageCollection::make($this->whenLoaded('images'), $this->query),
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
            'songs',
            'songs.themes',
            'songs.themes.anime',
            'members',
            'groups',
            'resources',
            'images',
        ];
    }
}
