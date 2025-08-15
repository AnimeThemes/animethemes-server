<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Wiki\AnimeResource\AnimeResourceAsField;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Wiki\AnimeResource;

class AnimeResourceType extends PivotType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Represents the association between an anime and an external resource.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new AnimeType(), AnimeResource::RELATION_ANIME)
                ->notNullable(),
            new BelongsToRelation(new ExternalResourceType(), AnimeResource::RELATION_RESOURCE)
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
            new AnimeResourceAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
