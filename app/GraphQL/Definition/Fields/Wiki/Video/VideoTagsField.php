<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Resolvers\ImplodeArrayResolver;
use App\Models\Wiki\Video;

/**
 * Class VideoTagsField.
 */
class VideoTagsField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_TAGS);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The attributes used to distinguish the file within the context of a theme';
    }

    /**
     * Get the directives of the field.
     *
     * @return array
     */
    public function directives(): array
    {
        return [
            'field' => [
                'resolver' => ImplodeArrayResolver::class,
            ],
        ];
    }

    /**
     * Resolve the field.
     *
     * @param  mixed  $root
     * @return mixed
     */
    public function resolve($root): mixed
    {
        // Not applicable for this field, as it is handled by the resolver.
        return '';
    }
}
