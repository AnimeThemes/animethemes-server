<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdUnbindableField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeMediaFormatField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeNameField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeSeasonField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeSlugField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeSynopsisField;
use App\GraphQL\Schema\Fields\Wiki\Anime\AnimeYearField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ImageableType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Schema\Types\Pivot\Wiki\AnimeSeriesType;
use App\GraphQL\Schema\Types\Pivot\Wiki\AnimeStudioType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeSynonymType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Schema\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Relations\HasManyRelation;
use App\GraphQL\Schema\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\Models\Wiki\Anime;

class AnimeType extends EloquentType implements ReportableType
{
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
            new MorphToManyRelation($this, ExternalResourceType::class, Anime::RELATION_RESOURCES, ResourceableType::class),
            new MorphToManyRelation($this, ImageType::class, Anime::RELATION_IMAGES, ImageableType::class),
            new BelongsToManyRelation($this, SeriesType::class, Anime::RELATION_SERIES, AnimeSeriesType::class),
            new BelongsToManyRelation($this, StudioType::class, Anime::RELATION_STUDIOS, AnimeStudioType::class),
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
