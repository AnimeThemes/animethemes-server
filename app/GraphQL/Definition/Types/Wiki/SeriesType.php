<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Wiki\Series\SeriesNameField;
use App\GraphQL\Definition\Fields\Wiki\Series\SeriesSlugField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\Models\Wiki\Series;

/**
 * Class SeriesType.
 */
class SeriesType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a collection of related anime.\n\nFor example, the Monogatari series is the collection of the Bakemonogatari anime and its related productions.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation(new AnimeType(), Series::RELATION_ANIME),
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
            new IdField(Series::ATTRIBUTE_ID),
            new SeriesNameField(),
            new SeriesSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
