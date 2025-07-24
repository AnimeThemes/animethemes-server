<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki\ExternalResource;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Base\NodeField;
use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\Pivot\Wiki\StudioResourceType;
use App\GraphQL\Definition\Types\Wiki\StudioType;

class ResourceStudioEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Resource Studio edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<StudioType>
     */
    public static function getNodeType(): string
    {
        return StudioType::class;
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
