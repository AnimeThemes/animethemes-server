<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
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
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            AnimeTheme::ATTRIBUTE_TYPE => $this->when($this->isAllowedField(AnimeTheme::ATTRIBUTE_TYPE), $this->type?->description),
            AnimeTheme::ATTRIBUTE_SEQUENCE => $this->when($this->isAllowedField(AnimeTheme::ATTRIBUTE_SEQUENCE), $this->sequence),
            AnimeTheme::ATTRIBUTE_GROUP => $this->when($this->isAllowedField(AnimeTheme::ATTRIBUTE_GROUP), $this->group),
            AnimeTheme::ATTRIBUTE_SLUG => $this->when($this->isAllowedField(AnimeTheme::ATTRIBUTE_SLUG), $this->slug),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            AnimeTheme::RELATION_ANIME => AnimeResource::make($this->whenLoaded(AnimeTheme::RELATION_ANIME), $this->query),
            AnimeTheme::RELATION_SONG => SongResource::make($this->whenLoaded(AnimeTheme::RELATION_SONG), $this->query),
            AnimeTheme::RELATION_ENTRIES => EntryCollection::make($this->whenLoaded(AnimeTheme::RELATION_ENTRIES), $this->query),
        ];
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new ThemeSchema();
    }
}
