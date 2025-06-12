<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\AnimeThemeSequenceField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\AnimeThemeTypeField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Definition\Types\Wiki\SongType;
use App\GraphQL\Definition\Types\Wiki\ThemeGroupType;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * Class AnimeThemeType.
 */
class AnimeThemeType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents an OP or ED sequence for an anime.\n\nFor example, the anime Bakemonogatari has five OP anime themes and one ED anime theme.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeType(), AnimeTheme::RELATION_ANIME, nullable: false),
            new HasManyRelation(new AnimeThemeEntryType(), AnimeTheme::RELATION_ENTRIES),
            new BelongsToRelation(new ThemeGroupType(), AnimeTheme::RELATION_GROUP),
            new BelongsToRelation(new SongType(), AnimeTheme::RELATION_SONG),
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
            new IdField(AnimeTheme::ATTRIBUTE_ID),
            new AnimeThemeTypeField(),
            new LocalizedEnumField(new AnimeThemeTypeField()),
            new AnimeThemeSequenceField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
