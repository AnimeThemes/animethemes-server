<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdUnbindableField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeMediaFormatField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeNameField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSeasonField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSlugField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSynopsisField;
use App\GraphQL\Definition\Fields\Wiki\Anime\AnimeYearField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeImageType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeSeriesType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeStudioType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeSynonymType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\MorphToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Anime;

class AnimeType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return "Represents a production with at least one opening or ending sequence.\n\nFor example, Bakemonogatari is an anime production with five opening sequences and one ending sequence.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new HasManyRelation(new AnimeSynonymType(), Anime::RELATION_SYNONYMS),
            new HasManyRelation(new AnimeThemeType(), Anime::RELATION_THEMES),
            new BelongsToManyRelation($this, ImageType::class, Anime::RELATION_IMAGES, AnimeImageType::class),
            new BelongsToManyRelation($this, SeriesType::class, Anime::RELATION_SERIES, AnimeSeriesType::class),
            new BelongsToManyRelation($this, StudioType::class, Anime::RELATION_STUDIOS, AnimeStudioType::class),
            new MorphToManyRelation($this, ExternalResourceType::class, Anime::RELATION_RESOURCES, ResourceableType::class),
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
            new IdUnbindableField(Anime::ATTRIBUTE_ID),
            new AnimeNameField(),
            new AnimeMediaFormatField(),
            new LocalizedEnumField(new AnimeMediaFormatField()),
            new AnimeSeasonField(),
            new LocalizedEnumField(new AnimeSeasonField()),
            new AnimeSlugField(),
            new AnimeSynopsisField(),
            new AnimeYearField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
