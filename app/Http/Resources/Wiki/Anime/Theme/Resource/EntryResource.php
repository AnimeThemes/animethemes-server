<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Theme\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class EntryResource.
 *
 * @mixin AnimeThemeEntry
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
     * @param  Query  $query
     * @return void
     */
    public function __construct(AnimeThemeEntry|MissingValue|null $entry, Query $query)
    {
        parent::__construct($entry, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_VERSION)) {
            $result[AnimeThemeEntry::ATTRIBUTE_VERSION] = $this->version;
        }

        if ($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_EPISODES)) {
            $result[AnimeThemeEntry::ATTRIBUTE_EPISODES] = $this->episodes;
        }

        if ($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_NSFW)) {
            $result[AnimeThemeEntry::ATTRIBUTE_NSFW] = $this->nsfw;
        }

        if ($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_SPOILER)) {
            $result[AnimeThemeEntry::ATTRIBUTE_SPOILER] = $this->spoiler;
        }

        if ($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_NOTES)) {
            $result[AnimeThemeEntry::ATTRIBUTE_NOTES] = $this->notes;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        $result[AnimeThemeEntry::RELATION_THEME] = ThemeResource::make($this->whenLoaded(AnimeThemeEntry::RELATION_THEME), $this->query);
        $result[AnimeThemeEntry::RELATION_VIDEOS] = VideoCollection::make($this->whenLoaded(AnimeThemeEntry::RELATION_VIDEOS), $this->query);

        return $result;
    }
}
