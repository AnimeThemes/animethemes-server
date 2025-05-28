<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistResource;

use App\GraphQL\Definition\Fields\StringField;
use App\Pivots\Wiki\ArtistResource;

/**
 * Class ArtistResourceAsField.
 */
class ArtistResourceAsField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ArtistResource::ATTRIBUTE_AS);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Used to distinguish resources that map to the same artist';
    }
}
