<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Pivot\Morph;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Pivot\Morph\Resourceable\ResourceableAsField;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Fields\Relations\MorphToRelation;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Types\Wiki\ExternalResourceType;
use App\GraphQL\Schema\Unions\ResourceableUnion;
use App\Pivots\Morph\Resourceable;

class ResourceableType extends PivotType
{
    public function description(): string
    {
        return 'Represents the association between a resourceable object and an external resource.';
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

            new BelongsToRelation(new ExternalResourceType(), Resourceable::RELATION_RESOURCE)
                ->nonNullable(),
            new MorphToRelation(new ResourceableUnion(), Resourceable::RELATION_RESOURCEABLE)
                ->nonNullable(),
        ];
    }
}
