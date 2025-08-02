<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Anime;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\AnimeThemeSequenceField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\AnimeThemeSlugField;
use App\GraphQL\Definition\Fields\Wiki\Anime\Theme\AnimeThemeTypeField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Definition\Types\Wiki\SongType;
use App\GraphQL\Definition\Types\Wiki\ThemeGroupType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return "Represents an OP or ED sequence for an anime.\n\nFor example, the anime Bakemonogatari has five OP anime themes and one ED anime theme.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeType(), AnimeTheme::RELATION_ANIME)
                ->notNullable(),
            new HasManyRelation(new AnimeThemeEntryType(), AnimeTheme::RELATION_ENTRIES),
            new BelongsToRelation(new ThemeGroupType(), AnimeTheme::RELATION_GROUP),
            new BelongsToRelation(new SongType(), AnimeTheme::RELATION_SONG),
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
            new IdField(AnimeTheme::ATTRIBUTE_ID, AnimeTheme::class),
            new AnimeThemeTypeField(),
            new LocalizedEnumField(new AnimeThemeTypeField()),
            new AnimeThemeSequenceField(),
            new AnimeThemeSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
