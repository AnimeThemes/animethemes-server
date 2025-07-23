<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki;

use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\Wiki\StudioType;

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
     * @return class-string<StudioType>
     */
    public static function getNodeType(): string
    {
        return StudioType::class;
    }
}
