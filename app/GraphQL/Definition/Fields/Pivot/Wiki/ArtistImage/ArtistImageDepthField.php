<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistImage;

use App\GraphQL\Attributes\UseField;
use App\GraphQL\Definition\Fields\IntField;
use App\GraphQL\Resolvers\PivotResolver;
use App\Pivots\Wiki\ArtistImage;

#[UseField(PivotResolver::class)]
class ArtistImageDepthField extends IntField
{
    public function __construct()
    {
        parent::__construct(ArtistImage::ATTRIBUTE_DEPTH);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to sort the artist images';
    }
}
