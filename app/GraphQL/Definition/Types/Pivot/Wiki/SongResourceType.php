<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\SongResoure\SongResourceAsField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;
use App\GraphQL\Definition\Types\Wiki\SongType;
use App\Pivots\Wiki\SongResource;

/**
 * Class SongResourceType.
 */
class SongResourceType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the association between an song and an external resource.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new SongType(), SongResource::RELATION_SONG, nullable: false),
            new BelongsToRelation(new ExternalResourceType(), SongResource::RELATION_RESOURCE, nullable: false),
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
            new SongResourceAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
