<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeSequenceField extends IntField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_SEQUENCE);
    }

    public function description(): string
    {
        return 'The numeric ordering of the theme';
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            'min:0',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            'min:0',
        ];
    }
}
