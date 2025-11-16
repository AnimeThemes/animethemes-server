<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Pivot\Morph;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Pivot\Morph\Imageable\ImageableDepthField;
use App\GraphQL\Schema\Relations\BelongsToRelation;
use App\GraphQL\Schema\Relations\MorphToRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Types\Wiki\ImageType;
use App\GraphQL\Schema\Unions\ImageableUnion;
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
