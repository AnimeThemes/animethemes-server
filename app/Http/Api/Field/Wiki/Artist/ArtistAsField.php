<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Artist;

use App\Http\Api\Field\Field;
use App\Pivots\ArtistResource;

/**
 * Class ArtistAsField.
 */
class ArtistAsField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ArtistResource::ATTRIBUTE_AS);
    }
}
