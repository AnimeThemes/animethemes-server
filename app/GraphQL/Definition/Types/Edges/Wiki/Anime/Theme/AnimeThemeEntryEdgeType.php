<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Edges\Wiki\Anime\Theme;

use App\GraphQL\Definition\Types\Edges\BaseEdgeType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;

class AnimeThemeEntryEdgeType extends BaseEdgeType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Anime Theme Entry edge to use in simple belongs to many relationships';
    }

    /**
     * Get the node type for the edge.
     *
     * @return class-string<AnimeThemeEntryType>
     */
    public static function getNodeType(): string
    {
        return AnimeThemeEntryType::class;
    }
}
