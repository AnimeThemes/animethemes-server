<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki\ExternalResource;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Base\NodeField;
use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\Pivot\Wiki\SongResourceType;
use App\GraphQL\Definition\Types\Wiki\SongType;

class ResourceSongEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Resource Song edge to use in simple belongs to many relationships';
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

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new NodeField($this->getNodeType()),
            ...(new SongResourceType()->fields()),
        ];
    }
}
