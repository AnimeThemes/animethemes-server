<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Concerns\JsonApi\PerformsResourceCollectionSearch;
use App\JsonApi\Filter\Base\CreatedAtFilter;
use App\JsonApi\Filter\Base\DeletedAtFilter;
use App\JsonApi\Filter\Base\TrashedFilter;
use App\JsonApi\Filter\Base\UpdatedAtFilter;
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
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function (ArtistResource $resource) {
            return $resource->parser($this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
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
     * @return array
     */
    public static function allowedSortFields()
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
     * @return array
     */
    public static function filters()
    {
        return [
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
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
