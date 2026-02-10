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
use App\GraphQL\Schema\Fields\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Fields\Wiki\Image\ImageFacetField;
use App\GraphQL\Schema\Fields\Wiki\Image\ImageLinkField;
use App\GraphQL\Schema\Fields\Wiki\Image\ImagePathField;
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

            new MorphToManyRelation($this, new AnimeType(), Image::RELATION_ANIME, new ImageableType()),
            new MorphToManyRelation($this, new ArtistType(), Image::RELATION_ARTISTS, new ImageableType()),
            new MorphToManyRelation($this, new StudioType(), Image::RELATION_STUDIOS, new ImageableType()),
        ];
    }
}
