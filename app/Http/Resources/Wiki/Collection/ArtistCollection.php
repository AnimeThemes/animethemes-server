<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Filter\Wiki\Artist\ArtistIdFilter;
use App\Http\Api\Filter\Wiki\Artist\ArtistNameFilter;
use App\Http\Api\Filter\Wiki\Artist\ArtistSlugFilter;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;
use Illuminate\Http\Request;

/**
 * Class ArtistCollection.
 */
class ArtistCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artists';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Artist::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Artist $artist) {
            return ArtistResource::make($artist, $this->parser);
        })->all();
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

    /**
     * The sort field names a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedSortFields(): array
    {
        return [
            'artist_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'slug',
            'name',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return string[]
     */
    public static function filters(): array
    {
        return array_merge(
            parent::filters(),
            [
                ArtistIdFilter::class,
                ArtistNameFilter::class,
                ArtistSlugFilter::class,
            ]
        );
    }
}
