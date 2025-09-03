<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\BooleanField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryNsfwField extends BooleanField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_NSFW, nullable: false);
    }

    public function description(): string
    {
        return 'Is not safe for work content included?';
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
            'boolean',
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
            'boolean',
        ];
    }
}
