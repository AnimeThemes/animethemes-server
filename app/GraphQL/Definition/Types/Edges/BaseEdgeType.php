<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Base\NodeField;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\EloquentType;

/**
 * Class BaseEdgeType.
 */
abstract class BaseEdgeType extends BaseType implements HasFields
{
    /**
     * Get the node type for the edge.
     *
     * @return class-string<EloquentType>
     */
    abstract public static function getNodeType(): string;

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new NodeField($this->getNodeType()),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
