<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki;

use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\Wiki\SongType;

/**
 * Class SongEdgeType.
 */
class SongEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Song edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<SongType>
     */
    public static function getNodeType(): string
    {
        return SongType::class;
    }
}
