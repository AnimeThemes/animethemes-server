<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Auth;

use App\GraphQL\Definition\Types\Auth\PermissionType;
use App\GraphQL\Definition\Types\Edges\BaseEdgeType;

class PermissionEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Permission edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<PermissionType>
     */
    public static function getNodeType(): string
    {
        return PermissionType::class;
    }
}
