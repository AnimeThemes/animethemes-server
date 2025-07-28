<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Anime;

class AnimeSynopsisField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_SYNOPSIS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The brief summary of the anime';
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:65535',
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:65535',
        ];
    }
}
