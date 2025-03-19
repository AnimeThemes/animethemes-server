<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Anime\Theme;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Anime\Theme\Entry\EntryEpisodesField;
use App\Http\Api\Field\Wiki\Anime\Theme\Entry\EntryNotesField;
use App\Http\Api\Field\Wiki\Anime\Theme\Entry\EntryNsfwField;
use App\Http\Api\Field\Wiki\Anime\Theme\Entry\EntrySpoilerField;
use App\Http\Api\Field\Wiki\Anime\Theme\Entry\EntryThemeIdField;
use App\Http\Api\Field\Wiki\Anime\Theme\Entry\EntryVersionField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EntrySchema.
 */
class EntrySchema extends EloquentSchema implements SearchableSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return EntryResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), AnimeThemeEntry::RELATION_ANIME),
            new AllowedInclude(new ThemeSchema(), AnimeThemeEntry::RELATION_THEME),
            new AllowedInclude(new VideoSchema(), AnimeThemeEntry::RELATION_VIDEOS),
        ]);
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, AnimeThemeEntry::ATTRIBUTE_ID),
                new EntryEpisodesField($this),
                new EntryNotesField($this),
                new EntryNsfwField($this),
                new EntrySpoilerField($this),
                new EntryVersionField($this),
                new EntryThemeIdField($this),
            ],
        );
    }

    /**
     * Get the model of the schema.
     *
     * @return Model
     */
    public function model(): Model
    {
        return new AnimeThemeEntry();
    }
}
