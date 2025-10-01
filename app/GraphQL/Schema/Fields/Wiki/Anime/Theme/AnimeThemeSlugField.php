<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\Theme;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeSlugField extends StringField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_SLUG);
    }

    public function description(): string
    {
        return 'The slug that represents the anime theme.';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'max:192',
            'alpha_dash',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'max:192',
            'alpha_dash',
        ];
    }
}
