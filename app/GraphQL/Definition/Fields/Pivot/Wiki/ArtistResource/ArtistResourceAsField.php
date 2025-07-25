<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistResource;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Resolvers\PivotResolver;
use App\Pivots\Wiki\ArtistResource;

#[UseFieldDirective(PivotResolver::class)]
class ArtistResourceAsField extends StringField
{
    public function __construct()
    {
        parent::__construct(ArtistResource::ATTRIBUTE_AS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to distinguish resources that map to the same artist';
    }
}
