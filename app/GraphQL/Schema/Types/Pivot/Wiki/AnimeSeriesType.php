<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Pivot\Wiki;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Schema\Types\Wiki\SeriesType;
use App\Pivots\Wiki\AnimeSeries;

class AnimeSeriesType extends PivotType
{
    public function description(): string
    {
        return 'Represents the association between an anime and a series.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new CreatedAtField(),
            new UpdatedAtField(),

            new BelongsToRelation(new AnimeType(), AnimeSeries::RELATION_ANIME)
                ->nonNullable(),
            new BelongsToRelation(new SeriesType(), AnimeSeries::RELATION_SERIES)
                ->nonNullable(),
        ];
    }
}
