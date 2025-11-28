<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\SubmitableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdUnbindableField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Series\SeriesNameField;
use App\GraphQL\Schema\Fields\Wiki\Series\SeriesSlugField;
use App\GraphQL\Schema\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Wiki\AnimeSeriesType;
use App\Models\Wiki\Series;

class SeriesType extends EloquentType implements SubmitableType
{
    public function description(): string
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
            new BelongsToManyRelation($this, AnimeType::class, Series::RELATION_ANIME, AnimeSeriesType::class),
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
            new IdUnbindableField(Series::ATTRIBUTE_ID),
            new SeriesNameField(),
            new SeriesSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
