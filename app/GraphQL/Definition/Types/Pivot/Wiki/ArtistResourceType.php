<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistResource\ArtistResourceAsField;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Wiki\ArtistResource;

class ArtistResourceType extends PivotType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Represents the association between an artist and an external resource.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ArtistType(), ArtistResource::RELATION_ARTIST)
                ->notNullable(),
            new BelongsToRelation(new ExternalResourceType(), ArtistResource::RELATION_RESOURCE)
                ->notNullable(),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new ArtistResourceAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
