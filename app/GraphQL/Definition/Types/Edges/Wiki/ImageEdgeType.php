<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki;

use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\Wiki\ImageType;

class ImageEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Image edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<ImageType>
     */
    public static function getNodeType(): string
    {
        return ImageType::class;
    }
}
