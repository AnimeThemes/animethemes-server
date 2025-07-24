<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Series\SeriesNameField;
use App\GraphQL\Definition\Fields\Wiki\Series\SeriesSlugField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Edges\Wiki\AnimeEdgeType;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Wiki\Series;

class SeriesType extends EloquentType implements HasFields, HasRelations
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
            new BelongsToManyRelation(new AnimeEdgeType(), Series::RELATION_ANIME),
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
            new IdField(Series::ATTRIBUTE_ID),
            new SeriesNameField(),
            new SeriesSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
