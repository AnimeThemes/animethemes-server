<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Morph;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Morph\Resourceable\ResourceableAsField;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;
use App\GraphQL\Definition\Unions\ResourceableUnion;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\MorphToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Morph\Resourceable;

class ResourceableType extends PivotType implements ReportableType
{
    public function description(): string
    {
        return 'Represents the association between a resourceable object and an external resource.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ExternalResourceType(), Resourceable::RELATION_RESOURCE)
                ->notNullable(),
            new MorphToRelation(new ResourceableUnion(), Resourceable::RELATION_RESOURCEABLE)
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
            new ResourceableAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
