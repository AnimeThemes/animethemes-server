<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\List;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ImageType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\List\PlaylistImage;

class PlaylistImageType extends PivotType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return 'Represents the association between a playlist and an image.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new PlaylistType(), PlaylistImage::RELATION_PLAYLIST)
                ->notNullable(),
            new BelongsToRelation(new ImageType(), PlaylistImage::RELATION_IMAGE)
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
