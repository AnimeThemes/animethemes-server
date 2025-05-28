<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\StudioResource\StudioResourceAsField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;
use App\GraphQL\Definition\Types\Wiki\StudioType;
use App\Pivots\Wiki\StudioResource;

/**
 * Class StudioResourceType.
 */
class StudioResourceType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the association between a studio and an external resource.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new StudioType(), StudioResource::RELATION_STUDIO, nullable: false),
            new BelongsToRelation(new ExternalResourceType(), StudioResource::RELATION_RESOURCE, nullable: false),
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
            new StudioResourceAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
