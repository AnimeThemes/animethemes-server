<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Theme\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class EntryResource.
 */
class EntryResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemeentry';

    /**
     * Create a new resource instance.
     *
     * @param  AnimeThemeEntry | MissingValue | null  $entry
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(AnimeThemeEntry|MissingValue|null $entry, ReadQuery $query)
    {
        parent::__construct($entry, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[AnimeThemeEntry::RELATION_THEME] = new ThemeResource($this->whenLoaded(AnimeThemeEntry::RELATION_THEME), $this->query);
        $result[AnimeThemeEntry::RELATION_VIDEOS] = new VideoCollection($this->whenLoaded(AnimeThemeEntry::RELATION_VIDEOS), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new EntrySchema();
    }
}
