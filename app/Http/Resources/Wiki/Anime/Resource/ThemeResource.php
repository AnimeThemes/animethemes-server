<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ThemeResource.
 *
 * @mixin AnimeTheme
 */
class ThemeResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animetheme';

    /**
     * Create a new resource instance.
     *
     * @param  AnimeTheme | MissingValue | null  $theme
     * @param  Query  $query
     * @return void
     */
    public function __construct(AnimeTheme|MissingValue|null $theme, Query $query)
    {
        parent::__construct($theme, $query);
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

        if ($this->isAllowedField(AnimeTheme::ATTRIBUTE_TYPE)) {
            $result[AnimeTheme::ATTRIBUTE_TYPE] = $this->type?->description;
        }

        if ($this->isAllowedField(AnimeTheme::ATTRIBUTE_SEQUENCE)) {
            $result[AnimeTheme::ATTRIBUTE_SEQUENCE] = $this->sequence;
        }

        if ($this->isAllowedField(AnimeTheme::ATTRIBUTE_GROUP)) {
            $result[AnimeTheme::ATTRIBUTE_GROUP] = $this->group;
        }

        if ($this->isAllowedField(AnimeTheme::ATTRIBUTE_SLUG)) {
            $result[AnimeTheme::ATTRIBUTE_SLUG] = $this->slug;
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

        $result[AnimeTheme::RELATION_ANIME] = AnimeResource::make($this->whenLoaded(AnimeTheme::RELATION_ANIME), $this->query);
        $result[AnimeTheme::RELATION_SONG] = SongResource::make($this->whenLoaded(AnimeTheme::RELATION_SONG), $this->query);
        $result[AnimeTheme::RELATION_ENTRIES] = EntryCollection::make($this->whenLoaded(AnimeTheme::RELATION_ENTRIES), $this->query);

        return $result;
    }
}
