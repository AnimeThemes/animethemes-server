<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Definition\Types\Wiki\SeriesType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Wiki\AnimeSeries;

class AnimeSeriesType extends PivotType implements ReportableType
{
    public function description(): string
    {
        return 'Represents the association between an anime and a series.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeType(), AnimeSeries::RELATION_ANIME)
                ->notNullable(),
            new BelongsToRelation(new SeriesType(), AnimeSeries::RELATION_SERIES)
                ->notNullable(),
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
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
