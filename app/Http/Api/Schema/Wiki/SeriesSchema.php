<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Series\SeriesNameField;
use App\Http\Api\Field\Wiki\Series\SeriesSlugField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Resources\Wiki\Resource\SeriesJsonResource;
use App\Models\Wiki\Series;

class SeriesSchema extends EloquentSchema implements SearchableSchema
{
    public function type(): string
    {
        return SeriesJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Series::RELATION_ANIME),

            // Undocumented paths needed for client builds
            new AllowedInclude(new ImageSchema(), 'anime.images'),
            new AllowedInclude(new VideoSchema(), 'anime.animethemes.animethemeentries.videos'),
            new AllowedInclude(new GroupSchema(), 'anime.animethemes.group'),
            new AllowedInclude(new SongSchema(), 'anime.animethemes.song'),
            new AllowedInclude(new SynonymSchema(), 'anime.animesynonyms'),
        ]);
    }

    /**
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, Series::ATTRIBUTE_ID),
                new SeriesNameField($this),
                new SeriesSlugField($this),
            ],
        );
    }
}
