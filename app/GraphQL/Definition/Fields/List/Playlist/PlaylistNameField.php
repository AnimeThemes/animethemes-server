<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\Playlist;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\List\Playlist;
use App\Rules\ModerationRule;

class PlaylistNameField extends StringField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The title of the playlist';
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
            'required',
            'string',
            'max:192',
            new ModerationRule(),
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
            'string',
            'max:192',
            new ModerationRule(),
        ];
    }
}
