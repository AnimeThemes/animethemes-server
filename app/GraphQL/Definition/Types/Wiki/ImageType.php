<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\Image\ImageFacetField;
use App\GraphQL\Definition\Fields\Wiki\Image\ImageLinkField;
use App\GraphQL\Definition\Fields\Wiki\Image\ImagePathField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeImageType;
use App\GraphQL\Definition\Types\Pivot\Wiki\ArtistImageType;
use App\GraphQL\Definition\Types\Pivot\Wiki\StudioImageType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Image;

class ImageType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
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
            new BelongsToManyRelation($this, AnimeType::class, Image::RELATION_ANIME, AnimeImageType::class),
            new BelongsToManyRelation($this, ArtistType::class, Image::RELATION_ARTISTS, ArtistImageType::class),
            new BelongsToManyRelation($this, StudioType::class, Image::RELATION_STUDIOS, StudioImageType::class),
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
