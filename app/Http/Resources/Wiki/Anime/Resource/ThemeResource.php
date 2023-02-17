<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Http\Resources\Wiki\Resource\SongResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;

/**
 * Class ThemeResource.
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
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[AnimeTheme::RELATION_ANIME] = new AnimeResource($this->whenLoaded(AnimeTheme::RELATION_ANIME), $this->query);
        $result[AnimeTheme::RELATION_SONG] = new SongResource($this->whenLoaded(AnimeTheme::RELATION_SONG), $this->query);
        $result[AnimeTheme::RELATION_ENTRIES] = new EntryCollection($this->whenLoaded(AnimeTheme::RELATION_ENTRIES), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ThemeSchema();
    }
}
