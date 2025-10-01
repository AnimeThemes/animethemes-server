<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Anime;

class AnimeSynopsisField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_SYNOPSIS);
    }

    public function description(): string
    {
        return 'The brief summary of the anime';
    }

    /**
     * @param  array<string, mixed>  $args
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
     * @param  array<string, mixed>  $args
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
