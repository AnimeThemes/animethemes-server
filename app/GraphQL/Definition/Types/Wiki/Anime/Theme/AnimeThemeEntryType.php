<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime\Theme;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntryEpisodesField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntryNotesField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntryNsfwField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntrySpoilerField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry\AnimeThemeEntryVersionField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeThemeEntryVideoType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
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
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
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
