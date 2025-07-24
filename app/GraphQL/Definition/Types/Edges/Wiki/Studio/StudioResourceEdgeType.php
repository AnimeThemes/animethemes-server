<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki\Studio;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Base\NodeField;
use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\Pivot\Wiki\StudioResourceType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;

class StudioResourceEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Studio External Resource edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<ExternalResourceType>
     */
    public static function getNodeType(): string
    {
        return ExternalResourceType::class;
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new NodeField($this->getNodeType()),
            ...(new StudioResourceType()->fields()),
        ];
    }
}
