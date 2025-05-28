<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistResource\ArtistResourceAsField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;
use App\Pivots\Wiki\ArtistResource;

/**
 * Class ArtistResourceType.
 */
class ArtistResourceType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the association between an artist and an external resource.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ArtistType(), ArtistResource::RELATION_ARTIST, nullable: false),
            new BelongsToRelation(new ExternalResourceType(), ArtistResource::RELATION_RESOURCE, nullable: false),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new ArtistResourceAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
