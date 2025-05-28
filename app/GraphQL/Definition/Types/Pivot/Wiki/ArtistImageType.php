<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistImage\ArtistImageDepthField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Definition\Types\Wiki\ImageType;
use App\Pivots\Wiki\ArtistImage;

/**
 * Class ArtistImageType.
 */
class ArtistImageType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the association between an artist and an image.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ArtistType(), ArtistImage::RELATION_ARTIST, nullable: false),
            new BelongsToRelation(new ImageType(), ArtistImage::RELATION_IMAGE, nullable: false),
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
            new ArtistImageDepthField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
