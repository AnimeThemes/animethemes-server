<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\SubmitableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Wiki\Image\ImageFacetField;
use App\GraphQL\Schema\Fields\Wiki\Image\ImageLinkField;
use App\GraphQL\Schema\Fields\Wiki\Image\ImagePathField;
use App\GraphQL\Schema\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ImageableType;
use App\Models\Wiki\Image;

class ImageType extends EloquentType implements SubmitableType
{
    public function description(): string
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
            new MorphToManyRelation($this, AnimeType::class, Image::RELATION_ANIME, ImageableType::class),
            new MorphToManyRelation($this, ArtistType::class, Image::RELATION_ARTISTS, ImageableType::class),
            new MorphToManyRelation($this, StudioType::class, Image::RELATION_STUDIOS, ImageableType::class),
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
            new IdField(Image::ATTRIBUTE_ID, Image::class),
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
