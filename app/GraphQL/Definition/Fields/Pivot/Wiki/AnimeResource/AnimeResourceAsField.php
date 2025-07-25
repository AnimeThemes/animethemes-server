<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\AnimeResource;

use App\GraphQL\Attributes\UseFieldDirective;
use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Resolvers\PivotResolver;
use App\Pivots\Wiki\AnimeResource;

#[UseFieldDirective(PivotResolver::class)]
class AnimeResourceAsField extends StringField
{
    public function __construct()
    {
        parent::__construct(AnimeResource::ATTRIBUTE_AS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to distinguish resources that map to the same anime';
    }
}
