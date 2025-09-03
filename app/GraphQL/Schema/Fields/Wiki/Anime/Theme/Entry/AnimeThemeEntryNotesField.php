<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryNotesField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_NOTES);
    }

    public function description(): string
    {
        return 'Any additional information for this sequence';
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
            'string',
            'max:192',
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
            'string',
            'max:192',
        ];
    }
}
