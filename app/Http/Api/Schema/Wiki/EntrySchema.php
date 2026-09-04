<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Entry\EntryEpisodesField;
use App\Http\Api\Field\Wiki\Entry\EntryNotesField;
use App\Http\Api\Field\Wiki\Entry\EntryNsfwField;
use App\Http\Api\Field\Wiki\Entry\EntrySpoilerField;
use App\Http\Api\Field\Wiki\Entry\EntryThemeIdField;
use App\Http\Api\Field\Wiki\Entry\EntryTrackCountField;
use App\Http\Api\Field\Wiki\Entry\EntryVersionField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\EntryJsonResource;
use App\Models\Wiki\Entry;

class EntrySchema extends EloquentSchema implements SearchableSchema
{
    public function type(): string
    {
        return EntryJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Entry::RELATION_ANIME),
            new AllowedInclude(new ThemeSchema(), Entry::RELATION_THEME),
            new AllowedInclude(new VideoSchema(), Entry::RELATION_VIDEOS),

            // Undocumented paths needed for client builds
            new AllowedInclude(new ImageSchema(), 'animetheme.anime.images'),
            new AllowedInclude(new ArtistSchema(), 'animetheme.song.artists'),
            new AllowedInclude(new GroupSchema(), Entry::RELATION_THEME_GROUP),
            new AllowedInclude(new AudioSchema(), 'videos.audio'),
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
                new IdField($this, Entry::ATTRIBUTE_ID),
                new EntryEpisodesField($this),
                new EntryNotesField($this),
                new EntryNsfwField($this),
                new EntrySpoilerField($this),
                new EntryVersionField($this),
                new EntryTrackCountField($this),
                new EntryThemeIdField($this),
            ],
        );
    }

    /**
     * Get the model of the schema.
     */
    public function model(): Entry
    {
        return new Entry();
    }
}
