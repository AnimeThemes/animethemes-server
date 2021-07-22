<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Filter\Wiki\Song\SongIdFilter;
use App\Http\Api\Filter\Wiki\Song\SongTitleFilter;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Song;
use Illuminate\Http\Request;

/**
 * Class SongCollection.
 */
class SongCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'songs';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Song::class;

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
        return $this->collection->map(function (Song $song) {
            return SongResource::make($song, $this->parser);
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
            'themes',
            'themes.anime',
            'artists',
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
            'song_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'title',
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
                SongIdFilter::class,
                SongTitleFilter::class,
            ]
        );
    }
}
