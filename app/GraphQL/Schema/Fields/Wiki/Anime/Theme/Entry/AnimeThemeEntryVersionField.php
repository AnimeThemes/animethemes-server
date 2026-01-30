<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\IntField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryVersionField extends IntField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_VERSION, nullable: false);
    }

    public function description(): string
    {
        return 'The version number of the theme';
    }

    /**
     * @param  array<string, mixed>  $args
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
