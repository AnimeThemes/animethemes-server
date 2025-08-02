<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ImageType;
use App\GraphQL\Definition\Types\Wiki\StudioType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Wiki\StudioImage;

class StudioImageType extends PivotType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Represents the association between a studio and an image.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new StudioType(), StudioImage::RELATION_STUDIO)
                ->notNullable(),
            new BelongsToRelation(new ImageType(), StudioImage::RELATION_IMAGE)
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
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
