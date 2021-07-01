<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\QueryParser;
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
 */
class ArtistResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'artist';

    /**
     * Create a new resource instance.
     *
     * @param Artist | MissingValue | null $artist
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(Artist | MissingValue | null $artist, QueryParser $parser)
    {
        parent::__construct($artist, $parser);
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
            'id' => $this->when($this->isAllowedField('id'), $this->artist_id),
            'name' => $this->when($this->isAllowedField('name'), $this->name),
            'slug' => $this->when($this->isAllowedField('slug'), $this->slug),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('artist_song', function () {
                return strval($this->pivot->as);
            }, $this->whenPivotLoaded('artist_member', function () {
                return strval($this->pivot->as);
            }, $this->whenPivotLoaded('artist_resource', function () {
                return strval($this->pivot->as);
            })))),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'songs' => SongCollection::make($this->whenLoaded('songs'), $this->parser),
            'members' => ArtistCollection::make($this->whenLoaded('members'), $this->parser),
            'groups' => ArtistCollection::make($this->whenLoaded('groups'), $this->parser),
            'resources' => ExternalResourceCollection::make($this->whenLoaded('resources'), $this->parser),
            'images' => ImageCollection::make($this->whenLoaded('images'), $this->parser),
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
