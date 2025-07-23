<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki;

use App\GraphQL\Definition\Types\Edges\BaseEdgeType;

/**
 * Class VideoEdgeType.
 */
class VideoEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Video edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<VideoEdgeType>
     */
    public static function getNodeType(): string
    {
        return VideoEdgeType::class;
    }
}
