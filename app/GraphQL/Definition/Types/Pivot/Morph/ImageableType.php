<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Morph;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Morph\Imageable\ImageableDepthField;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ImageType;
use App\GraphQL\Definition\Unions\ImageableUnion;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\MorphToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Morph\Imageable;

class ImageableType extends PivotType implements ReportableType
{
    public function description(): string
    {
        return 'Represents the association between a imageable object and an image.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ImageType(), Imageable::RELATION_IMAGE)
                ->notNullable(),
            new MorphToRelation(new ImageableUnion(), Imageable::RELATION_IMAGEABLE)
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
            new ImageableDepthField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
