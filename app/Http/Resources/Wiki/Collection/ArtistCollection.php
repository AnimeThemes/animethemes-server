<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (Artist $artist) => ArtistResource::make($artist, $this->query))->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new ArtistSchema();
    }
}
