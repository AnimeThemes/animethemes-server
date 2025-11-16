<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Pivot\Morph;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Pivot\Morph\Resourceable\ResourceableAsField;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Types\Wiki\ExternalResourceType;
use App\GraphQL\Schema\Unions\ResourceableUnion;
use App\GraphQL\Schema\Relations\BelongsToRelation;
use App\GraphQL\Schema\Relations\MorphToRelation;
use App\GraphQL\Schema\Relations\Relation;
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
