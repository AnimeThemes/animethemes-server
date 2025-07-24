<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki\Artist;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Base\NodeField;
use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\Pivot\Wiki\ArtistMemberType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;

class ArtistMemberEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Artist Member edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<ArtistType>
     */
    public static function getNodeType(): string
    {
        return ArtistType::class;
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
            ...(new ArtistMemberType()->fields()),
        ];
    }
}
