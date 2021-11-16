<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Theme\Collection;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\Request;

/**
 * Class EntryCollection.
 */
class EntryCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemeentries';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = AnimeThemeEntry::class;

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
        return $this->collection->map(fn (AnimeThemeEntry $entry) => EntryResource::make($entry, $this->query))->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new EntrySchema();
    }
}
