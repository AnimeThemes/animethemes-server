<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeMediaFormatField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeNameField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSeasonField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSlugField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSynopsisField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYearField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeSynonymType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use App\Models\Wiki\Anime;

/**
 * Class AnimeType.
 */
class AnimeType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a production with at least one opening or ending sequence.\n\nFor example, Bakemonogatari is an anime production with five opening sequences and one ending sequence.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new HasManyRelation(new AnimeSynonymType(), Anime::RELATION_SYNONYMS),
            new HasManyRelation(new AnimeThemeType(), Anime::RELATION_THEMES),
            new BelongsToManyRelation(new ImageType(), Anime::RELATION_IMAGES),
            new BelongsToManyRelation(new ExternalResourceType(), Anime::RELATION_RESOURCES),
            new BelongsToManyRelation(new SeriesType(), Anime::RELATION_SERIES),
            new BelongsToManyRelation(new StudioType(), Anime::RELATION_STUDIOS),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new IdField(Anime::ATTRIBUTE_ID),
            new AnimeNameField(),
            new AnimeMediaFormatField(),
            new AnimeSeasonField(),
            new AnimeSlugField(),
            new AnimeSynopsisField(),
            new AnimeYearField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
