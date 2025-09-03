<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Anime\Theme;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntryEpisodesField;
use App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntryNotesField;
use App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntryNsfwField;
use App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntrySpoilerField;
use App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntryVersionField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Schema\Types\Pivot\Wiki\AnimeThemeEntryVideoType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Schema\Types\Wiki\ExternalResourceType;
use App\GraphQL\Schema\Types\Wiki\VideoType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\MorphToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryType extends EloquentType implements ReportableType
{
    public function description(): string
    {
        return "Represents a version of an anime theme.\n\nFor example, the ED theme of the Bakemonogatari anime has three anime theme entries to represent three versions.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeThemeType(), AnimeThemeEntry::RELATION_THEME)
                ->notNullable(),
            new BelongsToManyRelation($this, VideoType::class, AnimeThemeEntry::RELATION_VIDEOS, AnimeThemeEntryVideoType::class),
            new MorphToManyRelation($this, ExternalResourceType::class, AnimeThemeEntry::RELATION_RESOURCES, ResourceableType::class),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(AnimeThemeEntry::ATTRIBUTE_ID, AnimeThemeEntry::class),
            new AnimeThemeEntryEpisodesField(),
            new AnimeThemeEntryNotesField(),
            new AnimeThemeEntryNsfwField(),
            new AnimeThemeEntrySpoilerField(),
            new AnimeThemeEntryVersionField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
