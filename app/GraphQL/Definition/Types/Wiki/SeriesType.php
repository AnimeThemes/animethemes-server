<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\HasRelations;
use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Series\SeriesNameField;
use App\GraphQL\Definition\Fields\Wiki\Series\SeriesSlugField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeSeriesType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Series;

class SeriesType extends EloquentType implements HasRelations, ReportableType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return "Represents a collection of related anime.\n\nFor example, the Monogatari series is the collection of the Bakemonogatari anime and its related productions.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation($this, SeriesType::class, Series::RELATION_ANIME, AnimeSeriesType::class),
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
            new IdField(Series::ATTRIBUTE_ID, Series::class),
            new SeriesNameField(),
            new SeriesSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
