<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Wiki\SongResoure\SongResourceAsField;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;
use App\GraphQL\Definition\Types\Wiki\SongType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Wiki\SongResource;

class SongResourceType extends PivotType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Represents the association between an song and an external resource.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new SongType(), SongResource::RELATION_SONG)
                ->notNullable(),
            new BelongsToRelation(new ExternalResourceType(), SongResource::RELATION_RESOURCE)
                ->notNullable(),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
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
