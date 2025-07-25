<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Resolvers\ImplodeArrayResolver;
use App\Models\Wiki\Video;

#[UseFieldDirective(ImplodeArrayResolver::class)]
class VideoTagsField extends StringField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_TAGS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The attributes used to distinguish the file within the context of a theme';
    }

    /**
     * Resolve the field.
     */
    public function resolve($root): mixed
    {
        // Not applicable for this field, as it is handled by the resolver.
        return '';
    }
}
