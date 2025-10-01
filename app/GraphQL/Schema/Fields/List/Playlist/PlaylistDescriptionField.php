<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\Playlist;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\List\Playlist;
use App\Rules\ModerationRule;

class PlaylistDescriptionField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_DESCRIPTION);
    }

    public function description(): string
    {
        return 'The description of the playlist';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:1000',
            new ModerationRule(),
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
            'max:1000',
            new ModerationRule(),
        ];
    }
}
