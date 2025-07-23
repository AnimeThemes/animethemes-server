<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\List;

use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\List\PlaylistType;

/**
 * Class PlaylistEdgeType.
 */
class PlaylistEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Playlist edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<PlaylistType>
     */
    public static function getNodeType(): string
    {
        return PlaylistType::class;
    }
}
