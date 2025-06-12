<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime\Theme;

use App\Contracts\GraphQL\HasFields;
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
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

/**
 * Class AnimeThemeEntryType.
 */
class AnimeThemeEntryType extends EloquentType implements HasFields
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a version of an anime theme.\n\nFor example, the ED theme of the Bakemonogatari anime has three anime theme entries to represent three versions.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeThemeType(), AnimeThemeEntry::RELATION_THEME, nullable: false),
            new BelongsToManyRelation(new VideoType(), AnimeThemeEntry::RELATION_VIDEOS),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array<int, Field>
     */
    public function fields(): array
    {
        return [
            new IdField(AnimeThemeEntry::ATTRIBUTE_ID),
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
