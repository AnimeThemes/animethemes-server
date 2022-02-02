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
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            AnimeThemeEntry::ATTRIBUTE_VERSION => $this->when($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_VERSION), $this->version),
            AnimeThemeEntry::ATTRIBUTE_EPISODES => $this->when($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_EPISODES), $this->episodes),
            AnimeThemeEntry::ATTRIBUTE_NSFW => $this->when($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_NSFW), $this->nsfw),
            AnimeThemeEntry::ATTRIBUTE_SPOILER => $this->when($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_SPOILER), $this->spoiler),
            AnimeThemeEntry::ATTRIBUTE_NOTES => $this->when($this->isAllowedField(AnimeThemeEntry::ATTRIBUTE_NOTES), $this->notes),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            AnimeThemeEntry::RELATION_THEME => ThemeResource::make($this->whenLoaded(AnimeThemeEntry::RELATION_THEME), $this->query),
            AnimeThemeEntry::RELATION_VIDEOS => VideoCollection::make($this->whenLoaded(AnimeThemeEntry::RELATION_VIDEOS), $this->query),
        ];
    }
}
