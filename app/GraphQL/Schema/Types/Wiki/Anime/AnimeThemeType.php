<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Anime;

use App\Contracts\GraphQL\Types\SubmitableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Wiki\Anime\Theme\AnimeThemeSequenceField;
use App\GraphQL\Schema\Fields\Wiki\Anime\Theme\AnimeThemeSlugField;
use App\GraphQL\Schema\Fields\Wiki\Anime\Theme\AnimeThemeTypeField;
use App\GraphQL\Schema\Relations\BelongsToRelation;
use App\GraphQL\Schema\Relations\HasManyRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Schema\Types\Wiki\SongType;
use App\GraphQL\Schema\Types\Wiki\ThemeGroupType;
use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeType extends EloquentType implements SubmitableType
{
    public function description(): string
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
    public function fieldClasses(): array
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
