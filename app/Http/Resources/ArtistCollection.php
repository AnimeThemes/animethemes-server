<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\PerformsResourceCollectionQuery;
use App\JsonApi\Traits\PerformsResourceCollectionSearch;
use Illuminate\Support\Str;

class ArtistCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery, PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'artists';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($artist) {
            return ArtistResource::make($artist, $this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @var array
     */
    public static function allowedIncludePaths()
    {
        return [
            'songs',
            'songs.themes',
            'songs.themes.anime',
            'members',
            'groups',
            'externalResources',
            'images',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static function allowedSortFields()
    {
        return [
            'artist_id',
            'created_at',
            'updated_at',
            'slug',
            'name',
        ];
    }

    /**
     * Resolve the related collection resource from the relation name.
     * We are assuming a convention of "{Relation}Collection".
     *
     * @param string $allowedIncludePath
     * @return string
     */
    protected static function relation($allowedIncludePath)
    {
        $relatedModel = Str::ucfirst(Str::singular(Str::of($allowedIncludePath)->explode('.')->last()));

        // Member and Group attributes do not follow convention
        if ($relatedModel === 'Member' || $relatedModel === 'Group') {
            $relatedModel = 'Artist';
        }

        return "\\App\\Http\\Resources\\{$relatedModel}Collection";
    }
}
