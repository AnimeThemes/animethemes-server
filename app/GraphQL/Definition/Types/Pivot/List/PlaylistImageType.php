<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\List;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ImageType;
use App\Pivots\List\PlaylistImage;

class PlaylistImageType extends PivotType implements HasFields, HasRelations
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
            new BelongsToRelation(new PlaylistType(), PlaylistImage::RELATION_PLAYLIST, nullable: false),
            new BelongsToRelation(new ImageType(), PlaylistImage::RELATION_IMAGE, nullable: false),
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
