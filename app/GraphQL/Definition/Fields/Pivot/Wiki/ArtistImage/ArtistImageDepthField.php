<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistImage;

use App\GraphQL\Definition\Fields\IntField;
use App\Pivots\Wiki\ArtistImage;

/**
 * Class ArtistImageDepthField.
 */
class ArtistImageDepthField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ArtistImage::ATTRIBUTE_DEPTH);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Used to sort the artist images';
    }
}
