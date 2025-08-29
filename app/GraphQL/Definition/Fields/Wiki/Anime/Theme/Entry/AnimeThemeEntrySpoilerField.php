<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\BooleanField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntrySpoilerField extends BooleanField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_SPOILER, nullable: false);
    }

    public function description(): string
    {
        return 'Is content included that may spoil the viewer?';
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
            'sometimes',
            'required',
            'boolean',
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
            'sometimes',
            'required',
            'boolean',
        ];
    }
}
