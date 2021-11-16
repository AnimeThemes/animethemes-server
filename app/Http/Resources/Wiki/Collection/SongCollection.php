<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\SongSchema;
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (Song $song) => SongResource::make($song, $this->query))->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new SongSchema();
    }
}
