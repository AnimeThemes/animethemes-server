<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Image\ImageFacetField;
use App\GraphQL\Definition\Fields\Wiki\Image\ImageLinkField;
use App\GraphQL\Definition\Fields\Wiki\Image\ImagePathField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Wiki\Image;

/**
 * Class ImageType.
 */
class ImageType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a visual component for another resource such as an anime or artist.\n\nFor example, the Bakemonogatari anime has two images to represent small and large cover images.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation(new AnimeType(), Image::RELATION_ANIME),
            new BelongsToManyRelation(new ArtistType(), Image::RELATION_ARTISTS, edgeType: 'ImageArtistEdge'),
            new BelongsToManyRelation(new StudioType(), Image::RELATION_STUDIOS),
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
            new IdField(Image::ATTRIBUTE_ID),
            new ImageFacetField(),
            new LocalizedEnumField(new ImageFacetField()),
            new ImagePathField(),
            new ImageLinkField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
