<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki;

use App\GraphQL\Definition\Types\Edges\BaseEdgeType;

/**
 * Class StudioEdgeType.
 */
class StudioEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Studio edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<StudioEdgeType>
     */
    public static function getNodeType(): string
    {
        return StudioEdgeType::class;
    }
}
